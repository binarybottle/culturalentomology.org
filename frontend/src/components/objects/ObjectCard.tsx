'use client';

import Link from 'next/link';
import { ExternalLink, MapPin } from 'lucide-react';
import { CulturalObject } from '@/lib/api';
import ImageWithFallback from '@/components/ui/ImageWithFallback';

interface ObjectCardProps {
  object: CulturalObject;
  priority?: boolean;
}

export default function ObjectCard({ object, priority = false }: ObjectCardProps) {
  const hasImage = object.primary_image?.thumbnail_url;
  const locationParts = [object.location.city, object.location.state, object.location.nation]
    .filter(Boolean);
  const locationString = locationParts.join(', ');

  return (
    <Link
      href={`/object/${object.object_id}`}
      className="object-card group block"
    >
      {/* Image */}
      <div className="relative aspect-[4/3] overflow-hidden bg-surface-100">
        {hasImage ? (
          <ImageWithFallback
            src={object.primary_image!.thumbnail_url}
            alt={object.title}
            fill
            className="object-cover transition-transform duration-500 group-hover:scale-105"
            priority={priority}
          />
        ) : (
          <div className="absolute inset-0 flex items-center justify-center text-surface-400">
            <svg className="w-16 h-16 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        )}
        
        {/* Object ID badge */}
        <div className="absolute top-2 right-2 px-2 py-0.5 bg-black/60 backdrop-blur-sm rounded text-xs text-white font-mono">
          #{object.object_id}
        </div>
      </div>

      {/* Content */}
      <div className="p-4">
        {/* Title */}
        <h3 className="font-display text-lg font-semibold text-surface-800 line-clamp-2 group-hover:text-primary-600 transition-colors">
          {object.title}
        </h3>

        {/* Creator and year */}
        {(object.creator || object.year) && (
          <p className="mt-1 text-sm text-surface-600">
            {object.creator}
            {object.creator && object.year && ', '}
            {object.year}
          </p>
        )}

        {/* Medium */}
        {object.medium && (
          <p className="mt-1 text-sm text-surface-500 line-clamp-1">
            {object.medium}
          </p>
        )}

        {/* Location */}
        {locationString && (
          <p className="mt-2 text-xs text-surface-400 flex items-center gap-1">
            <MapPin className="w-3 h-3" />
            {locationString}
          </p>
        )}

        {/* Categories */}
        {object.categories.length > 0 && (
          <div className="mt-3 flex flex-wrap gap-1.5">
            {object.categories.slice(0, 2).map((cat, i) => (
              <span key={i} className="category-pill">
                {cat}
              </span>
            ))}
            {object.categories.length > 2 && (
              <span className="text-xs text-surface-400">
                +{object.categories.length - 2}
              </span>
            )}
          </div>
        )}

        {/* Taxa */}
        {object.taxa.length > 0 && object.taxa[0].common_name && (
          <div className="mt-2 flex flex-wrap gap-1.5">
            <span className="taxon-pill">
              {object.taxa[0].common_name}
            </span>
          </div>
        )}
      </div>
    </Link>
  );
}

