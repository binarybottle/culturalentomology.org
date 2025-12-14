'use client';

import { useState, useEffect, useCallback } from 'react';
import { useSearchParams, useRouter } from 'next/navigation';
import { Suspense } from 'react';
import { Bug, Sparkles, ArrowRight, ChevronDown } from 'lucide-react';
import SearchBox from '@/components/search/SearchBox';
import ObjectGrid from '@/components/objects/ObjectGrid';
import FilterSidebar from '@/components/filters/FilterSidebar';
import { searchObjects, fetchObjects, fetchFilterOptions, CulturalObject, FilterOptions } from '@/lib/api';

export const runtime = 'edge';

function HomeContent() {
  const searchParams = useSearchParams();
  const router = useRouter();
  
  const initialQuery = searchParams.get('q') || '';
  const initialCategory = searchParams.get('category') || undefined;
  const initialTaxon = searchParams.get('taxon') || undefined;
  const initialNation = searchParams.get('nation') || undefined;
  const initialPage = parseInt(searchParams.get('page') || '1', 10);

  const [query, setQuery] = useState(initialQuery);
  const [objects, setObjects] = useState<CulturalObject[]>([]);
  const [loading, setLoading] = useState(false);
  const [total, setTotal] = useState(0);
  const [page, setPage] = useState(initialPage);
  const [hasSearched, setHasSearched] = useState(!!initialQuery);
  
  // Filters
  const [filterOptions, setFilterOptions] = useState<FilterOptions>({
    categories: [],
    taxonOrders: [],
    nations: [],
  });
  const [selectedCategory, setSelectedCategory] = useState(initialCategory);
  const [selectedTaxonOrder, setSelectedTaxonOrder] = useState(initialTaxon);
  const [selectedNation, setSelectedNation] = useState(initialNation);
  const [showFilters, setShowFilters] = useState(false);

  // Load filter options
  useEffect(() => {
    fetchFilterOptions()
      .then(setFilterOptions)
      .catch(console.error);
  }, []);

  // Update URL with current state
  const updateUrl = useCallback((newQuery: string, newPage: number, cat?: string, taxon?: string, nation?: string) => {
    const params = new URLSearchParams();
    if (newQuery) params.set('q', newQuery);
    if (newPage > 1) params.set('page', String(newPage));
    if (cat) params.set('category', cat);
    if (taxon) params.set('taxon', taxon);
    if (nation) params.set('nation', nation);
    
    const queryString = params.toString();
    router.push(queryString ? `/?${queryString}` : '/', { scroll: false });
  }, [router]);

  // Perform search
  const performSearch = useCallback(async (searchQuery: string, pageNum: number = 1) => {
    setLoading(true);
    setHasSearched(true);
    
    try {
      if (searchQuery) {
        const result = await searchObjects(searchQuery, {
          category: selectedCategory,
          taxonOrder: selectedTaxonOrder,
          nation: selectedNation,
          page: pageNum,
          pageSize: 20,
        });
        setObjects(result.results);
        setTotal(result.total);
      } else {
        const result = await fetchObjects({
          category: selectedCategory,
          taxonOrder: selectedTaxonOrder,
          nation: selectedNation,
          page: pageNum,
          pageSize: 20,
        });
        setObjects(result.objects);
        setTotal(result.total);
      }
    } catch (error) {
      console.error('Search error:', error);
      setObjects([]);
      setTotal(0);
    } finally {
      setLoading(false);
    }
  }, [selectedCategory, selectedTaxonOrder, selectedNation]);

  // Handle search submission
  const handleSearch = (newQuery: string) => {
    setQuery(newQuery);
    setPage(1);
    updateUrl(newQuery, 1, selectedCategory, selectedTaxonOrder, selectedNation);
    performSearch(newQuery, 1);
  };

  // Handle filter changes
  const handleFilterChange = (cat?: string, taxon?: string, nation?: string) => {
    setSelectedCategory(cat);
    setSelectedTaxonOrder(taxon);
    setSelectedNation(nation);
    setPage(1);
    updateUrl(query, 1, cat, taxon, nation);
  };

  // Initial load if query present
  useEffect(() => {
    if (initialQuery || initialCategory || initialTaxon || initialNation) {
      performSearch(initialQuery, initialPage);
    }
  }, []); // eslint-disable-line react-hooks/exhaustive-deps

  // Re-search when filters change
  useEffect(() => {
    if (hasSearched) {
      performSearch(query, 1);
    }
  }, [selectedCategory, selectedTaxonOrder, selectedNation]); // eslint-disable-line react-hooks/exhaustive-deps

  // Load more
  const loadMore = () => {
    const nextPage = page + 1;
    setPage(nextPage);
    updateUrl(query, nextPage, selectedCategory, selectedTaxonOrder, selectedNation);
    
    setLoading(true);
    const searchFn = query ? searchObjects : fetchObjects;
    const options = {
      category: selectedCategory,
      taxonOrder: selectedTaxonOrder,
      nation: selectedNation,
      page: nextPage,
      pageSize: 20,
    };
    
    (query ? searchObjects(query, options) : fetchObjects(options))
      .then(result => {
        const newObjects = 'results' in result ? result.results : result.objects;
        setObjects(prev => [...prev, ...newObjects]);
      })
      .catch(console.error)
      .finally(() => setLoading(false));
  };

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      {!hasSearched && (
        <section className="hero-section py-20 md:py-32 relative">
          <div className="container mx-auto px-4 relative z-10">
            <div className="max-w-3xl mx-auto text-center mb-12 fade-in">
              <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-100 text-primary-700 text-sm font-medium mb-6">
                <Sparkles className="w-4 h-4" />
                Explore cultural entomology
              </div>
              <h1 className="text-4xl md:text-6xl font-display font-bold text-surface-900 mb-6 leading-tight">
                Insects in Art, Literature,
                <br />
                <span className="text-primary-600">Music & Culture</span>
              </h1>
              <p className="text-lg md:text-xl text-surface-600 mb-8 leading-relaxed">
                Discover the profound influence of insects on human creativity throughout history—
                from ancient artifacts to contemporary art, across all cultures and media.
              </p>
            </div>

            {/* Search Box */}
            <SearchBox onSearch={handleSearch} autoFocus />

            {/* Quick stats */}
            <div className="flex flex-wrap justify-center gap-8 mt-12 text-center">
              <div className="fade-in" style={{ animationDelay: '0.2s' }}>
                <div className="text-3xl font-bold text-primary-600">{total || '534'}</div>
                <div className="text-sm text-surface-500">Objects</div>
              </div>
              <div className="fade-in" style={{ animationDelay: '0.3s' }}>
                <div className="text-3xl font-bold text-accent-600">{filterOptions.categories.length || '41'}</div>
                <div className="text-sm text-surface-500">Categories</div>
              </div>
              <div className="fade-in" style={{ animationDelay: '0.4s' }}>
                <div className="text-3xl font-bold text-surface-700">{filterOptions.nations.length || '34'}</div>
                <div className="text-sm text-surface-500">Countries</div>
              </div>
            </div>
          </div>

          {/* Decorative elements */}
          <div className="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-surface-50 to-transparent" />
        </section>
      )}

      {/* Search Results Section */}
      {hasSearched && (
        <section className="py-8">
          <div className="container mx-auto px-4">
            {/* Search bar (sticky when scrolling) */}
            <div className="mb-8">
              <SearchBox onSearch={handleSearch} initialQuery={query} />
            </div>

            {/* Results header */}
            <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
              <div>
                <h2 className="text-2xl font-display font-bold text-surface-800">
                  {query ? (
                    <>Results for <span className="text-primary-600">"{query}"</span></>
                  ) : (
                    'All Objects'
                  )}
                </h2>
                <p className="text-surface-500 mt-1">
                  {total.toLocaleString()} result{total !== 1 ? 's' : ''} found
                </p>
              </div>
              
              <button
                onClick={() => setShowFilters(!showFilters)}
                className="btn-secondary md:hidden"
              >
                Filters
                <ChevronDown className={`w-4 h-4 ml-2 transition-transform ${showFilters ? 'rotate-180' : ''}`} />
              </button>
            </div>

            {/* Main content with sidebar */}
            <div className="flex flex-col lg:flex-row gap-8">
              {/* Filter sidebar */}
              <aside className={`lg:w-64 flex-shrink-0 ${showFilters ? 'block' : 'hidden lg:block'}`}>
                <div className="lg:sticky lg:top-24">
                  <FilterSidebar
                    categories={filterOptions.categories}
                    taxonOrders={filterOptions.taxonOrders}
                    nations={filterOptions.nations}
                    selectedCategory={selectedCategory}
                    selectedTaxonOrder={selectedTaxonOrder}
                    selectedNation={selectedNation}
                    onCategoryChange={(cat) => handleFilterChange(cat, selectedTaxonOrder, selectedNation)}
                    onTaxonOrderChange={(taxon) => handleFilterChange(selectedCategory, taxon, selectedNation)}
                    onNationChange={(nation) => handleFilterChange(selectedCategory, selectedTaxonOrder, nation)}
                    onClearAll={() => handleFilterChange(undefined, undefined, undefined)}
                    resultCount={total}
                  />
                </div>
              </aside>

              {/* Results grid */}
              <div className="flex-1">
                <ObjectGrid objects={objects} loading={loading && page === 1} />
                
                {/* Load more button */}
                {objects.length < total && !loading && (
                  <div className="mt-12 text-center">
                    <button onClick={loadMore} className="btn-primary">
                      Load More
                      <ArrowRight className="w-4 h-4 ml-2" />
                    </button>
                    <p className="mt-2 text-sm text-surface-500">
                      Showing {objects.length} of {total.toLocaleString()}
                    </p>
                  </div>
                )}
                
                {loading && page > 1 && (
                  <div className="mt-8 text-center">
                    <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-primary-200 border-t-primary-600" />
                  </div>
                )}
              </div>
            </div>
          </div>
        </section>
      )}

      {/* Featured Categories (only on landing) */}
      {!hasSearched && (
        <section className="py-16 bg-white">
          <div className="container mx-auto px-4">
            <h2 className="text-2xl md:text-3xl font-display font-bold text-surface-800 text-center mb-12">
              Explore by Category
            </h2>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              {['Painting', 'Sculpture', 'Textile', 'Jewelry', 'Literature', 'Music', 'Film', 'Folk Art'].map((category) => (
                <button
                  key={category}
                  onClick={() => {
                    setSelectedCategory(category);
                    handleSearch('');
                  }}
                  className="p-6 rounded-xl bg-surface-50 hover:bg-surface-100 border border-surface-200 hover:border-primary-300 transition-all group"
                >
                  <h3 className="font-display text-lg font-semibold text-surface-700 group-hover:text-primary-600 transition-colors">
                    {category}
                  </h3>
                </button>
              ))}
            </div>
          </div>
        </section>
      )}
    </div>
  );
}

export default function HomePage() {
  return (
    <Suspense fallback={
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <Bug className="w-12 h-12 text-primary-600 animate-pulse mx-auto mb-4" />
          <p className="text-surface-500">Loading...</p>
        </div>
      </div>
    }>
      <HomeContent />
    </Suspense>
  );
}

