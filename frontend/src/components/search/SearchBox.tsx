'use client';

import { useState, useEffect, useRef } from 'react';
import { Search, X, Clock, ArrowRight } from 'lucide-react';

const RECENT_SEARCHES_KEY = 'ce_recent_searches';
const MAX_RECENT = 8;

// Example searches to show when input is empty
const EXAMPLE_SEARCHES = [
  { query: '+silk -"silk-screen"', description: 'silk but not silk-screen' },
  { query: 'butterfly painting', description: 'butterflies in paintings' },
  { query: 'beetle jewelry', description: 'beetles in jewelry' },
  { query: 'moth literature', description: 'moths in literature' },
  { query: 'bee symbolism', description: 'symbolic bees' },
];

interface SearchBoxProps {
  onSearch: (query: string) => void;
  initialQuery?: string;
  autoFocus?: boolean;
}

export default function SearchBox({ onSearch, initialQuery = '', autoFocus = false }: SearchBoxProps) {
  const [query, setQuery] = useState(initialQuery);
  const [showSuggestions, setShowSuggestions] = useState(false);
  const [recentSearches, setRecentSearches] = useState<string[]>([]);
  const inputRef = useRef<HTMLInputElement>(null);
  const containerRef = useRef<HTMLDivElement>(null);

  // Load recent searches
  useEffect(() => {
    const stored = localStorage.getItem(RECENT_SEARCHES_KEY);
    if (stored) {
      try {
        setRecentSearches(JSON.parse(stored));
      } catch (e) {
        console.error('Error loading recent searches:', e);
      }
    }
  }, []);

  // Handle Cmd/Ctrl+K
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        inputRef.current?.focus();
        setShowSuggestions(true);
      }
      if (e.key === 'Escape') {
        setShowSuggestions(false);
        inputRef.current?.blur();
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  // Click outside to close suggestions
  useEffect(() => {
    const handleClickOutside = (e: MouseEvent) => {
      if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
        setShowSuggestions(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const saveRecentSearch = (searchQuery: string) => {
    if (!searchQuery.trim()) return;
    const updated = [
      searchQuery,
      ...recentSearches.filter(s => s !== searchQuery)
    ].slice(0, MAX_RECENT);
    setRecentSearches(updated);
    localStorage.setItem(RECENT_SEARCHES_KEY, JSON.stringify(updated));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (query.trim()) {
      saveRecentSearch(query.trim());
      onSearch(query.trim());
      setShowSuggestions(false);
    }
  };

  const handleSuggestionClick = (suggestion: string) => {
    setQuery(suggestion);
    saveRecentSearch(suggestion);
    onSearch(suggestion);
    setShowSuggestions(false);
  };

  const clearQuery = () => {
    setQuery('');
    inputRef.current?.focus();
  };

  return (
    <div ref={containerRef} className="relative w-full max-w-2xl mx-auto">
      <form onSubmit={handleSubmit}>
        <div className="relative">
          <Search className="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-surface-400" />
          <input
            ref={inputRef}
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            onFocus={() => setShowSuggestions(true)}
            placeholder="Search insects in art, literature, music, and more..."
            className="search-input pl-14"
            autoFocus={autoFocus}
          />
          {query && (
            <button
              type="button"
              onClick={clearQuery}
              className="absolute right-16 top-1/2 -translate-y-1/2 p-1 rounded hover:bg-surface-100 transition-colors"
            >
              <X className="w-4 h-4 text-surface-400" />
            </button>
          )}
          <button
            type="submit"
            className="absolute right-3 top-1/2 -translate-y-1/2 p-2 rounded-lg bg-primary-600 hover:bg-primary-700 text-white transition-colors"
          >
            <ArrowRight className="w-5 h-5" />
          </button>
        </div>
      </form>

      {/* Suggestions dropdown */}
      {showSuggestions && (
        <div className="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-surface-200 overflow-hidden z-50 fade-in">
          {/* Recent searches */}
          {recentSearches.length > 0 && (
            <div className="p-3 border-b border-surface-100">
              <div className="flex items-center gap-2 text-xs text-surface-500 font-medium px-2 mb-2">
                <Clock className="w-3 h-3" />
                Recent Searches
              </div>
              <div className="space-y-1">
                {recentSearches.slice(0, 4).map((search, i) => (
                  <button
                    key={i}
                    onClick={() => handleSuggestionClick(search)}
                    className="w-full text-left px-3 py-2 rounded-lg hover:bg-surface-50 text-surface-700 text-sm transition-colors"
                  >
                    {search}
                  </button>
                ))}
              </div>
            </div>
          )}

          {/* Example searches */}
          <div className="p-3">
            <div className="text-xs text-surface-500 font-medium px-2 mb-2">
              Try searching for
            </div>
            <div className="space-y-1">
              {EXAMPLE_SEARCHES.map((example, i) => (
                <button
                  key={i}
                  onClick={() => handleSuggestionClick(example.query)}
                  className="w-full text-left px-3 py-2 rounded-lg hover:bg-surface-50 flex items-center justify-between group transition-colors"
                >
                  <code className="text-sm text-primary-600 bg-primary-50 px-2 py-0.5 rounded">
                    {example.query}
                  </code>
                  <span className="text-xs text-surface-400 group-hover:text-surface-600 transition-colors">
                    {example.description}
                  </span>
                </button>
              ))}
            </div>
          </div>

          {/* Search tips */}
          <div className="px-4 py-3 bg-surface-50 border-t border-surface-100">
            <p className="text-xs text-surface-500">
              <strong>Tip:</strong> Use <code className="bg-surface-200 px-1 rounded">+word</code> to require a word, 
              <code className="bg-surface-200 px-1 rounded mx-1">-word</code> to exclude, 
              and <code className="bg-surface-200 px-1 rounded">"exact phrase"</code> for phrases.
            </p>
          </div>
        </div>
      )}
    </div>
  );
}

