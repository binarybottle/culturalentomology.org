'use client';

import { useState } from 'react';
import { ChevronDown, X, Filter } from 'lucide-react';

interface FilterSidebarProps {
  categories: string[];
  taxonOrders: string[];
  nations: string[];
  selectedCategory?: string;
  selectedTaxonOrder?: string;
  selectedNation?: string;
  onCategoryChange: (category: string | undefined) => void;
  onTaxonOrderChange: (order: string | undefined) => void;
  onNationChange: (nation: string | undefined) => void;
  onClearAll: () => void;
  resultCount?: number;
}

export default function FilterSidebar({
  categories,
  taxonOrders,
  nations,
  selectedCategory,
  selectedTaxonOrder,
  selectedNation,
  onCategoryChange,
  onTaxonOrderChange,
  onNationChange,
  onClearAll,
  resultCount,
}: FilterSidebarProps) {
  const [expandedSections, setExpandedSections] = useState({
    category: true,
    taxon: true,
    nation: false,
  });

  const toggleSection = (section: keyof typeof expandedSections) => {
    setExpandedSections(prev => ({
      ...prev,
      [section]: !prev[section],
    }));
  };

  const hasActiveFilters = selectedCategory || selectedTaxonOrder || selectedNation;

  return (
    <div className="space-y-4">
      {/* Active filters summary */}
      {hasActiveFilters && (
        <div className="filter-section">
          <div className="flex items-center justify-between mb-3">
            <span className="filter-title flex items-center gap-2">
              <Filter className="w-4 h-4" />
              Active Filters
            </span>
            <button
              onClick={onClearAll}
              className="text-xs text-primary-600 hover:text-primary-700 font-medium"
            >
              Clear all
            </button>
          </div>
          <div className="flex flex-wrap gap-2">
            {selectedCategory && (
              <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-primary-100 text-primary-800 text-sm">
                {selectedCategory}
                <button
                  onClick={() => onCategoryChange(undefined)}
                  className="ml-1 hover:text-primary-600"
                >
                  <X className="w-3 h-3" />
                </button>
              </span>
            )}
            {selectedTaxonOrder && (
              <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-accent-100 text-accent-800 text-sm">
                {selectedTaxonOrder}
                <button
                  onClick={() => onTaxonOrderChange(undefined)}
                  className="ml-1 hover:text-accent-600"
                >
                  <X className="w-3 h-3" />
                </button>
              </span>
            )}
            {selectedNation && (
              <span className="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-200 text-surface-800 text-sm">
                {selectedNation}
                <button
                  onClick={() => onNationChange(undefined)}
                  className="ml-1 hover:text-surface-600"
                >
                  <X className="w-3 h-3" />
                </button>
              </span>
            )}
          </div>
        </div>
      )}

      {/* Categories */}
      <div className="filter-section">
        <button
          onClick={() => toggleSection('category')}
          className="w-full flex items-center justify-between"
        >
          <span className="filter-title">Category</span>
          <ChevronDown
            className={`w-4 h-4 text-surface-400 transition-transform ${
              expandedSections.category ? 'rotate-180' : ''
            }`}
          />
        </button>
        {expandedSections.category && (
          <div className="mt-3 space-y-1 max-h-48 overflow-y-auto">
            {categories.map((cat) => (
              <button
                key={cat}
                onClick={() => onCategoryChange(selectedCategory === cat ? undefined : cat)}
                className={`w-full text-left px-3 py-2 rounded-lg text-sm transition-colors ${
                  selectedCategory === cat
                    ? 'bg-primary-100 text-primary-800 font-medium'
                    : 'hover:bg-surface-50 text-surface-600'
                }`}
              >
                {cat}
              </button>
            ))}
          </div>
        )}
      </div>

      {/* Insect Orders */}
      <div className="filter-section">
        <button
          onClick={() => toggleSection('taxon')}
          className="w-full flex items-center justify-between"
        >
          <span className="filter-title">Insect Order</span>
          <ChevronDown
            className={`w-4 h-4 text-surface-400 transition-transform ${
              expandedSections.taxon ? 'rotate-180' : ''
            }`}
          />
        </button>
        {expandedSections.taxon && (
          <div className="mt-3 space-y-1 max-h-48 overflow-y-auto">
            {taxonOrders.map((order) => (
              <button
                key={order}
                onClick={() => onTaxonOrderChange(selectedTaxonOrder === order ? undefined : order)}
                className={`w-full text-left px-3 py-2 rounded-lg text-sm transition-colors ${
                  selectedTaxonOrder === order
                    ? 'bg-accent-100 text-accent-800 font-medium'
                    : 'hover:bg-surface-50 text-surface-600'
                }`}
              >
                {order}
              </button>
            ))}
          </div>
        )}
      </div>

      {/* Nations */}
      <div className="filter-section">
        <button
          onClick={() => toggleSection('nation')}
          className="w-full flex items-center justify-between"
        >
          <span className="filter-title">Country</span>
          <ChevronDown
            className={`w-4 h-4 text-surface-400 transition-transform ${
              expandedSections.nation ? 'rotate-180' : ''
            }`}
          />
        </button>
        {expandedSections.nation && (
          <div className="mt-3 space-y-1 max-h-48 overflow-y-auto">
            {nations.map((nation) => (
              <button
                key={nation}
                onClick={() => onNationChange(selectedNation === nation ? undefined : nation)}
                className={`w-full text-left px-3 py-2 rounded-lg text-sm transition-colors ${
                  selectedNation === nation
                    ? 'bg-surface-200 text-surface-800 font-medium'
                    : 'hover:bg-surface-50 text-surface-600'
                }`}
              >
                {nation}
              </button>
            ))}
          </div>
        )}
      </div>

      {/* Result count */}
      {resultCount !== undefined && (
        <div className="text-center text-sm text-surface-500 py-2">
          {resultCount.toLocaleString()} result{resultCount !== 1 ? 's' : ''}
        </div>
      )}
    </div>
  );
}

