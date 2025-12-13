'use client';

import { useState, useEffect } from 'react';
import { useParams } from 'next/navigation';

// Configure for Cloudflare Pages Edge Runtime
export const runtime = 'edge';
import Link from 'next/link';
import { 
  ArrowLeft, 
  ExternalLink, 
  MapPin, 
  Calendar, 
  Tag, 
  BookOpen,
  Bug,
  ChevronLeft,
  ChevronRight,
  X
} from 'lucide-react';
import { fetchObject, CulturalObject } from '@/lib/api';
import ImageWithFallback from '@/components/ui/ImageWithFallback';

export default function ObjectDetailPage() {
  const params = useParams();
  const objectId = parseInt(params.id as string, 10);
  
  const [object, setObject] = useState<CulturalObject | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [selectedImageIndex, setSelectedImageIndex] = useState(0);
  const [lightboxOpen, setLightboxOpen] = useState(false);

  useEffect(() => {
    async function loadObject() {
      try {
        const data = await fetchObject(objectId);
        setObject(data);
      } catch (err) {
        console.error('Error loading object:', err);
        setError('Object not found');
      } finally {
        setLoading(false);
      }
    }
    
    if (objectId) {
      loadObject();
    }
  }, [objectId]);

  // Keyboard navigation for lightbox
  useEffect(() => {
    if (!lightboxOpen || !object) return;
    
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        setLightboxOpen(false);
      } else if (e.key === 'ArrowLeft' && selectedImageIndex > 0) {
        setSelectedImageIndex(prev => prev - 1);
      } else if (e.key === 'ArrowRight' && selectedImageIndex < object.images.length - 1) {
        setSelectedImageIndex(prev => prev + 1);
      }
    };
    
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [lightboxOpen, selectedImageIndex, object]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <Bug className="w-12 h-12 text-primary-600 animate-pulse mx-auto mb-4" />
          <p className="text-surface-500">Loading...</p>
        </div>
      </div>
    );
  }

  if (error || !object) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <h1 className="text-2xl font-display font-bold text-surface-800 mb-4">
            Object Not Found
          </h1>
          <p className="text-surface-600 mb-6">
            The requested object could not be found.
          </p>
          <Link href="/" className="btn-primary">
            <ArrowLeft className="w-4 h-4 mr-2" />
            Back to Search
          </Link>
        </div>
      </div>
    );
  }

  const locationParts = [object.location.city, object.location.state, object.location.nation].filter(Boolean);
  const hasMultipleImages = object.images.length > 1;

  return (
    <div className="min-h-screen bg-surface-50">
      {/* Breadcrumb */}
      <div className="bg-white border-b border-surface-200">
        <div className="container mx-auto px-4 py-4">
          <Link 
            href="/" 
            className="inline-flex items-center gap-2 text-surface-500 hover:text-primary-600 transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Back to Search
          </Link>
        </div>
      </div>

      {/* Main content */}
      <div className="container mx-auto px-4 py-8">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
          {/* Images Section */}
          <div className="space-y-4">
            {/* Main Image */}
            <div 
              className="image-frame aspect-[4/3] relative cursor-zoom-in"
              onClick={() => setLightboxOpen(true)}
            >
              {object.images.length > 0 ? (
                <ImageWithFallback
                  src={object.images[selectedImageIndex]?.url || '/images/placeholder.jpg'}
                  alt={object.title}
                  fill
                  className="object-contain"
                  priority
                />
              ) : (
                <div className="absolute inset-0 flex items-center justify-center text-surface-400">
                  <Bug className="w-20 h-20 opacity-20" />
                </div>
              )}
              
              {/* Image navigation arrows */}
              {hasMultipleImages && (
                <>
                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      setSelectedImageIndex(prev => Math.max(0, prev - 1));
                    }}
                    disabled={selectedImageIndex === 0}
                    className="absolute left-2 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 text-white hover:bg-black/70 disabled:opacity-30 transition-all"
                  >
                    <ChevronLeft className="w-6 h-6" />
                  </button>
                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      setSelectedImageIndex(prev => Math.min(object.images.length - 1, prev + 1));
                    }}
                    disabled={selectedImageIndex === object.images.length - 1}
                    className="absolute right-2 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 text-white hover:bg-black/70 disabled:opacity-30 transition-all"
                  >
                    <ChevronRight className="w-6 h-6" />
                  </button>
                </>
              )}
            </div>

            {/* Thumbnail strip */}
            {hasMultipleImages && (
              <div className="flex gap-2 overflow-x-auto pb-2">
                {object.images.map((img, index) => (
                  <button
                    key={index}
                    onClick={() => setSelectedImageIndex(index)}
                    className={`flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all ${
                      index === selectedImageIndex 
                        ? 'border-primary-500 ring-2 ring-primary-200' 
                        : 'border-transparent hover:border-surface-300'
                    }`}
                  >
                    <img
                      src={img.thumbnail_url}
                      alt={`${object.title} - Image ${index + 1}`}
                      className="w-full h-full object-cover"
                    />
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Details Section */}
          <div className="space-y-6">
            {/* Header */}
            <div>
              <div className="flex items-center gap-2 mb-3">
                <span className="px-3 py-1 bg-surface-200 text-surface-600 rounded-full text-sm font-mono">
                  #{object.object_id}
                </span>
              </div>
              <h1 className="text-3xl md:text-4xl font-display font-bold text-surface-900 leading-tight">
                {object.title}
              </h1>
            </div>

            {/* Creator info */}
            {(object.creator || object.year) && (
              <div className="flex flex-wrap items-center gap-x-2 text-lg text-surface-700">
                {object.creator && <span className="font-medium">{object.creator}</span>}
                {object.creator && object.year && <span className="text-surface-400">,</span>}
                {object.year && <span>{object.year}</span>}
              </div>
            )}

            {/* Medium and dimensions */}
            {(object.medium || object.dimensions) && (
              <p className="text-surface-600">
                {object.medium}
                {object.medium && object.dimensions && ' '}
                {object.dimensions && <span className="text-surface-500">({object.dimensions})</span>}
              </p>
            )}

            {/* Description */}
            {object.description && (
              <div className="bg-white rounded-xl p-6 border border-surface-200">
                <p className="text-surface-700 leading-relaxed whitespace-pre-line">
                  {object.description}
                </p>
              </div>
            )}

            {/* Categories */}
            {object.categories.length > 0 && (
              <div className="info-row">
                <span className="info-label flex items-center gap-2">
                  <Tag className="w-4 h-4" />
                  Categories
                </span>
                <div className="info-value flex flex-wrap gap-2">
                  {object.categories.map((cat, i) => (
                    <Link
                      key={i}
                      href={`/?category=${encodeURIComponent(cat)}`}
                      className="category-pill hover:bg-primary-200 transition-colors"
                    >
                      {cat}
                    </Link>
                  ))}
                </div>
              </div>
            )}

            {/* Location */}
            {locationParts.length > 0 && (
              <div className="info-row">
                <span className="info-label flex items-center gap-2">
                  <MapPin className="w-4 h-4" />
                  Location
                </span>
                <span className="info-value">
                  {locationParts.join(', ')}
                </span>
              </div>
            )}

            {/* Time period */}
            {object.time_period && (
              <div className="info-row">
                <span className="info-label flex items-center gap-2">
                  <Calendar className="w-4 h-4" />
                  Time Period
                </span>
                <span className="info-value">{object.time_period}</span>
              </div>
            )}

            {/* Taxa */}
            {object.taxa.length > 0 && (
              <div className="bg-accent-50 rounded-xl p-6 border border-accent-200">
                <h3 className="font-semibold text-accent-800 mb-4 flex items-center gap-2">
                  <Bug className="w-5 h-5" />
                  Taxonomic Information
                </h3>
                <div className="space-y-4">
                  {object.taxa.map((taxon, i) => (
                    <div key={i} className="taxon-tree">
                      {taxon.common_name && (
                        <div className="taxon-tree-item font-medium text-accent-900">
                          {taxon.common_name}
                        </div>
                      )}
                      {taxon.order && (
                        <div className="taxon-tree-item">
                          <span className="text-surface-500">Order:</span> {taxon.order}
                        </div>
                      )}
                      {taxon.family && (
                        <div className="taxon-tree-item">
                          <span className="text-surface-500">Family:</span> {taxon.family}
                        </div>
                      )}
                      {taxon.species && (
                        <div className="taxon-tree-item italic">
                          <span className="text-surface-500 not-italic">Species:</span> {taxon.species}
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Collection */}
            {object.collection && (
              <div className="info-row">
                <span className="info-label flex items-center gap-2">
                  <BookOpen className="w-4 h-4" />
                  Collection
                </span>
                <span className="info-value">{object.collection}</span>
              </div>
            )}

            {/* Citation */}
            {object.citation && (
              <div className="info-row">
                <span className="info-label">Citation</span>
                <span className="info-value text-sm">{object.citation}</span>
              </div>
            )}

            {/* External URL */}
            {object.url && (
              <a
                href={object.url}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 font-medium"
              >
                <ExternalLink className="w-4 h-4" />
                View Original Source
              </a>
            )}
          </div>
        </div>
      </div>

      {/* Lightbox */}
      {lightboxOpen && object.images.length > 0 && (
        <div 
          className="fixed inset-0 bg-black/95 z-50 flex items-center justify-center"
          onClick={() => setLightboxOpen(false)}
        >
          <button
            onClick={() => setLightboxOpen(false)}
            className="absolute top-4 right-4 p-2 text-white/70 hover:text-white transition-colors"
          >
            <X className="w-8 h-8" />
          </button>
          
          <div 
            className="relative max-w-[90vw] max-h-[90vh]"
            onClick={(e) => e.stopPropagation()}
          >
            <img
              src={object.images[selectedImageIndex]?.url}
              alt={object.title}
              className="max-w-full max-h-[90vh] object-contain"
            />
            
            {hasMultipleImages && (
              <>
                <button
                  onClick={() => setSelectedImageIndex(prev => Math.max(0, prev - 1))}
                  disabled={selectedImageIndex === 0}
                  className="absolute left-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-white/10 text-white hover:bg-white/20 disabled:opacity-30 transition-all"
                >
                  <ChevronLeft className="w-8 h-8" />
                </button>
                <button
                  onClick={() => setSelectedImageIndex(prev => Math.min(object.images.length - 1, prev + 1))}
                  disabled={selectedImageIndex === object.images.length - 1}
                  className="absolute right-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-white/10 text-white hover:bg-white/20 disabled:opacity-30 transition-all"
                >
                  <ChevronRight className="w-8 h-8" />
                </button>
                <div className="absolute bottom-4 left-1/2 -translate-x-1/2 px-4 py-2 bg-black/50 rounded-full text-white text-sm">
                  {selectedImageIndex + 1} / {object.images.length}
                </div>
              </>
            )}
          </div>
        </div>
      )}
    </div>
  );
}

