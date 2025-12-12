/**
 * API client for Cultural Entomology Database
 * 
 * This module provides type-safe functions for all data operations.
 * Uses Supabase for database queries and Cloudflare Images for image delivery.
 */

import {
  getObjects as dbGetObjects,
  getObject as dbGetObject,
  searchObjects as dbSearchObjects,
  getCategories as dbGetCategories,
  getTaxonOrders as dbGetTaxonOrders,
  getNations as dbGetNations,
  submitObject as dbSubmitObject,
  DbObject,
} from './supabase';

import {
  getImageUrl,
  getThumbnailUrl,
  getObjectImageUrls,
  ImageVariant,
} from './cloudflare-images';

// ============================================
// Type Definitions (API response types)
// ============================================

export interface ObjectImage {
  url: string;
  thumbnail_url: string;
  filename: string | null;
}

export interface Taxon {
  common_name: string | null;
  order: string | null;
  family: string | null;
  species: string | null;
}

export interface Location {
  city: string | null;
  state: string | null;
  nation: string | null;
}

export interface CulturalObject {
  id: number;
  object_id: number;
  title: string;
  description: string | null;
  categories: string[];
  creator: string | null;
  year: string | null;
  medium: string | null;
  dimensions: string | null;
  time_period: string | null;
  location: Location;
  taxa: Taxon[];
  url: string | null;
  collection: string | null;
  citation: string | null;
  images: ObjectImage[];
  primary_image: ObjectImage | null;
}

export interface SearchResult {
  results: CulturalObject[];
  total: number;
  page: number;
  page_size: number;
  query: string;
}

export interface FilterOptions {
  categories: string[];
  taxonOrders: string[];
  nations: string[];
}

// ============================================
// Transform Functions (DB -> API types)
// ============================================

function transformObject(dbObj: DbObject): CulturalObject {
  // Extract categories
  const categories = [
    dbObj.category1,
    dbObj.category2,
    dbObj.category3,
    dbObj.category4,
  ].filter((c): c is string => Boolean(c?.trim()));

  // Extract taxa
  const taxa: Taxon[] = [];
  
  if (dbObj.taxon_common_name || dbObj.taxon_order || dbObj.taxon_family || dbObj.taxon_species) {
    taxa.push({
      common_name: dbObj.taxon_common_name,
      order: dbObj.taxon_order,
      family: dbObj.taxon_family,
      species: dbObj.taxon_species,
    });
  }
  
  if (dbObj.taxon_common_name2 || dbObj.taxon_order2 || dbObj.taxon_family2 || dbObj.taxon_species2) {
    taxa.push({
      common_name: dbObj.taxon_common_name2,
      order: dbObj.taxon_order2,
      family: dbObj.taxon_family2,
      species: dbObj.taxon_species2,
    });
  }
  
  if (dbObj.taxon_common_name3 || dbObj.taxon_order3 || dbObj.taxon_family3 || dbObj.taxon_species3) {
    taxa.push({
      common_name: dbObj.taxon_common_name3,
      order: dbObj.taxon_order3,
      family: dbObj.taxon_family3,
      species: dbObj.taxon_species3,
    });
  }
  
  if (dbObj.taxon_common_name4 || dbObj.taxon_order4 || dbObj.taxon_family4 || dbObj.taxon_species4) {
    taxa.push({
      common_name: dbObj.taxon_common_name4,
      order: dbObj.taxon_order4,
      family: dbObj.taxon_family4,
      species: dbObj.taxon_species4,
    });
  }

  // Extract images
  const filenames = [
    dbObj.filename1,
    dbObj.filename2,
    dbObj.filename3,
    dbObj.filename4,
    dbObj.filename5,
    dbObj.filename6,
    dbObj.filename7,
    dbObj.filename8,
    dbObj.filename9,
    dbObj.filename10,
  ];
  
  const cloudflareIds = [
    dbObj.cloudflare_image_id1,
    dbObj.cloudflare_image_id2,
    dbObj.cloudflare_image_id3,
    dbObj.cloudflare_image_id4,
    dbObj.cloudflare_image_id5,
    dbObj.cloudflare_image_id6,
    dbObj.cloudflare_image_id7,
    dbObj.cloudflare_image_id8,
    dbObj.cloudflare_image_id9,
    dbObj.cloudflare_image_id10,
  ];

  const images: ObjectImage[] = [];
  
  for (let i = 0; i < filenames.length; i++) {
    const filename = filenames[i];
    const cfId = cloudflareIds[i];
    
    if (filename || cfId) {
      images.push({
        url: getImageUrl(cfId, filename, 'large'),
        thumbnail_url: getThumbnailUrl(cfId, filename),
        filename,
      });
    }
  }

  return {
    id: dbObj.id,
    object_id: dbObj.pk_object_id,
    title: dbObj.title || 'Untitled',
    description: dbObj.description,
    categories,
    creator: dbObj.creator,
    year: dbObj.year,
    medium: dbObj.object_medium,
    dimensions: dbObj.object_dimensions,
    time_period: dbObj.time_period,
    location: {
      city: dbObj.city,
      state: dbObj.state,
      nation: dbObj.nation,
    },
    taxa,
    url: dbObj.url,
    collection: dbObj.collection,
    citation: dbObj.citation,
    images,
    primary_image: images[0] || null,
  };
}

// ============================================
// API Functions (public interface)
// ============================================

/**
 * Fetch objects with optional filters
 */
export async function fetchObjects(options?: {
  category?: string;
  taxonOrder?: string;
  nation?: string;
  page?: number;
  pageSize?: number;
}): Promise<{ objects: CulturalObject[]; total: number; page: number; pageSize: number }> {
  const { data, total, page, pageSize } = await dbGetObjects(options);
  
  return {
    objects: data.map(transformObject),
    total,
    page,
    pageSize,
  };
}

/**
 * Fetch a single object by ID
 */
export async function fetchObject(objectId: number): Promise<CulturalObject> {
  const data = await dbGetObject(objectId);
  
  if (!data) {
    throw new Error(`Object ${objectId} not found`);
  }
  
  return transformObject(data);
}

/**
 * Search objects
 */
export async function searchObjects(
  query: string,
  options?: {
    category?: string;
    taxonOrder?: string;
    nation?: string;
    page?: number;
    pageSize?: number;
  }
): Promise<SearchResult> {
  const result = await dbSearchObjects(query, options);
  
  return {
    results: result.results.map(transformObject),
    total: result.total,
    page: result.page,
    page_size: result.pageSize,
    query: result.query,
  };
}

/**
 * Get all filter options
 */
export async function fetchFilterOptions(): Promise<FilterOptions> {
  const [categories, taxonOrders, nations] = await Promise.all([
    dbGetCategories(),
    dbGetTaxonOrders(),
    dbGetNations(),
  ]);
  
  return {
    categories,
    taxonOrders,
    nations,
  };
}

/**
 * Submit a new object contribution
 */
export async function submitContribution(data: {
  title: string;
  description?: string;
  category1?: string;
  creator?: string;
  year?: string;
  object_medium?: string;
  nation?: string;
  taxon_common_name?: string;
  taxon_order?: string;
  url?: string;
  submit_first: string;
  submit_last: string;
  submit_email: string;
}): Promise<CulturalObject> {
  const result = await dbSubmitObject(data);
  return transformObject(result);
}

