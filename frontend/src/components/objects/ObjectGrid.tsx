'use client';

import { CulturalObject } from '@/lib/api';
import ObjectCard from './ObjectCard';

interface ObjectGridProps {
  objects: CulturalObject[];
  loading?: boolean;
}

export default function ObjectGrid({ objects, loading = false }: ObjectGridProps) {
  if (loading) {
    return (
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        {Array.from({ length: 8 }).map((_, i) => (
          <div key={i} className="object-card">
            <div className="aspect-[4/3] skeleton" />
            <div className="p-4 space-y-3">
              <div className="h-6 skeleton w-3/4" />
              <div className="h-4 skeleton w-1/2" />
              <div className="h-4 skeleton w-2/3" />
              <div className="flex gap-2">
                <div className="h-6 skeleton w-16 rounded-full" />
                <div className="h-6 skeleton w-20 rounded-full" />
              </div>
            </div>
          </div>
        ))}
      </div>
    );
  }

  if (objects.length === 0) {
    return (
      <div className="text-center py-16">
        <div className="w-20 h-20 mx-auto mb-6 rounded-full bg-surface-100 flex items-center justify-center">
          <svg className="w-10 h-10 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 className="text-xl font-display font-semibold text-surface-700 mb-2">
          No results found
        </h3>
        <p className="text-surface-500 max-w-md mx-auto">
          Try adjusting your search terms or filters. You can use <code className="bg-surface-100 px-1 rounded">+word</code> to 
          require a term or <code className="bg-surface-100 px-1 rounded">-word</code> to exclude it.
        </p>
      </div>
    );
  }

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 stagger-fade">
      {objects.map((object, index) => (
        <ObjectCard 
          key={object.object_id} 
          object={object} 
          priority={index < 4}
        />
      ))}
    </div>
  );
}

