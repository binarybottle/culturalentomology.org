'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { Bug, Grid, List, ChevronRight } from 'lucide-react';
import { fetchFilterOptions, fetchObjects, CulturalObject, FilterOptions } from '@/lib/api';
import ObjectGrid from '@/components/objects/ObjectGrid';

export const runtime = 'edge';

type ViewMode = 'categories' | 'orders' | 'countries' | 'gallery';

export default function BrowsePage() {
  const [viewMode, setViewMode] = useState<ViewMode>('categories');
  const [filterOptions, setFilterOptions] = useState<FilterOptions>({
    categories: [],
    taxonOrders: [],
    nations: [],
  });
  const [selectedFilter, setSelectedFilter] = useState<string | null>(null);
  const [objects, setObjects] = useState<CulturalObject[]>([]);
  const [loading, setLoading] = useState(false);
  const [total, setTotal] = useState(0);
  const [page, setPage] = useState(1);

  // Load filter options
  useEffect(() => {
    fetchFilterOptions()
      .then(setFilterOptions)
      .catch(console.error);
  }, []);

  // Load objects when filter is selected
  useEffect(() => {
    if (!selectedFilter) return;
    
    setLoading(true);
    setPage(1);
    
    const options: any = { page: 1, pageSize: 24 };
    
    if (viewMode === 'categories') {
      options.category = selectedFilter;
    } else if (viewMode === 'orders') {
      options.taxonOrder = selectedFilter;
    } else if (viewMode === 'countries') {
      options.nation = selectedFilter;
    }
    
    fetchObjects(options)
      .then(result => {
        setObjects(result.objects);
        setTotal(result.total);
      })
      .catch(console.error)
      .finally(() => setLoading(false));
  }, [selectedFilter, viewMode]);

  const loadMore = () => {
    const nextPage = page + 1;
    setPage(nextPage);
    setLoading(true);
    
    const options: any = { page: nextPage, pageSize: 24 };
    
    if (viewMode === 'categories') {
      options.category = selectedFilter;
    } else if (viewMode === 'orders') {
      options.taxonOrder = selectedFilter;
    } else if (viewMode === 'countries') {
      options.nation = selectedFilter;
    }
    
    fetchObjects(options)
      .then(result => {
        setObjects(prev => [...prev, ...result.objects]);
      })
      .catch(console.error)
      .finally(() => setLoading(false));
  };

  const renderBrowseList = (items: string[], type: string) => (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      {items.map((item) => (
        <button
          key={item}
          onClick={() => setSelectedFilter(item)}
          className={`p-4 rounded-xl text-left transition-all ${
            selectedFilter === item
              ? 'bg-primary-100 border-2 border-primary-500 text-primary-800'
              : 'bg-white border border-surface-200 hover:border-primary-300 hover:shadow-md'
          }`}
        >
          <div className="flex items-center justify-between">
            <span className="font-medium text-surface-800">{item}</span>
            <ChevronRight className={`w-5 h-5 transition-colors ${
              selectedFilter === item ? 'text-primary-600' : 'text-surface-400'
            }`} />
          </div>
        </button>
      ))}
    </div>
  );

  return (
    <div className="min-h-screen bg-surface-50">
      {/* Header */}
      <section className="bg-white border-b border-surface-200 py-8">
        <div className="container mx-auto px-4">
          <h1 className="text-3xl font-display font-bold text-surface-900 mb-4">
            Browse the Collection
          </h1>
          <p className="text-surface-600 max-w-2xl mb-6">
            Explore cultural entomology objects by category, insect order, or country of origin.
          </p>
          
          {/* View mode tabs */}
          <div className="flex flex-wrap gap-2">
            <button
              onClick={() => { setViewMode('categories'); setSelectedFilter(null); }}
              className={`px-4 py-2 rounded-lg font-medium transition-colors ${
                viewMode === 'categories'
                  ? 'bg-primary-600 text-white'
                  : 'bg-surface-100 text-surface-600 hover:bg-surface-200'
              }`}
            >
              <Grid className="w-4 h-4 inline mr-2" />
              Categories
            </button>
            <button
              onClick={() => { setViewMode('orders'); setSelectedFilter(null); }}
              className={`px-4 py-2 rounded-lg font-medium transition-colors ${
                viewMode === 'orders'
                  ? 'bg-accent-600 text-white'
                  : 'bg-surface-100 text-surface-600 hover:bg-surface-200'
              }`}
            >
              <Bug className="w-4 h-4 inline mr-2" />
              Insect Orders
            </button>
            <button
              onClick={() => { setViewMode('countries'); setSelectedFilter(null); }}
              className={`px-4 py-2 rounded-lg font-medium transition-colors ${
                viewMode === 'countries'
                  ? 'bg-surface-700 text-white'
                  : 'bg-surface-100 text-surface-600 hover:bg-surface-200'
              }`}
            >
              <List className="w-4 h-4 inline mr-2" />
              Countries
            </button>
          </div>
        </div>
      </section>

      {/* Content */}
      <section className="py-8">
        <div className="container mx-auto px-4">
          {/* Browse list */}
          {!selectedFilter && (
            <div className="fade-in">
              {viewMode === 'categories' && renderBrowseList(filterOptions.categories, 'category')}
              {viewMode === 'orders' && renderBrowseList(filterOptions.taxonOrders, 'order')}
              {viewMode === 'countries' && renderBrowseList(filterOptions.nations, 'country')}
              
              {/* Empty state */}
              {((viewMode === 'categories' && filterOptions.categories.length === 0) ||
                (viewMode === 'orders' && filterOptions.taxonOrders.length === 0) ||
                (viewMode === 'countries' && filterOptions.nations.length === 0)) && (
                <div className="text-center py-16">
                  <Bug className="w-16 h-16 text-surface-300 mx-auto mb-4" />
                  <p className="text-surface-500">Loading...</p>
                </div>
              )}
            </div>
          )}

          {/* Selected filter results */}
          {selectedFilter && (
            <div className="fade-in">
              {/* Breadcrumb */}
              <div className="mb-6 flex items-center gap-2 text-sm">
                <button
                  onClick={() => setSelectedFilter(null)}
                  className="text-primary-600 hover:text-primary-700"
                >
                  {viewMode === 'categories' ? 'Categories' : viewMode === 'orders' ? 'Insect Orders' : 'Countries'}
                </button>
                <ChevronRight className="w-4 h-4 text-surface-400" />
                <span className="text-surface-700 font-medium">{selectedFilter}</span>
                <span className="text-surface-500 ml-2">
                  ({total.toLocaleString()} object{total !== 1 ? 's' : ''})
                </span>
              </div>

              {/* Object grid */}
              <ObjectGrid objects={objects} loading={loading && page === 1} />
              
              {/* Load more */}
              {objects.length < total && !loading && (
                <div className="mt-12 text-center">
                  <button onClick={loadMore} className="btn-primary">
                    Load More
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
          )}
        </div>
      </section>
    </div>
  );
}

