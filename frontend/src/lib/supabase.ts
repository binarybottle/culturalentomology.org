/**
 * Supabase Client for Cultural Entomology Database
 * 
 * This module provides a typed Supabase client for accessing the database.
 * Uses environment variables for configuration.
 */

import { createClient, SupabaseClient } from '@supabase/supabase-js';

// Environment variables (set these in .env.local or Cloudflare Pages settings)
const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL || '';
const supabaseAnonKey = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY || '';

// Create a single Supabase client for the entire app
let supabase: SupabaseClient;

if (supabaseUrl && supabaseAnonKey) {
  supabase = createClient(supabaseUrl, supabaseAnonKey);
} else {
  // Create a mock client for build time - queries will fail but build will succeed
  console.warn('Supabase credentials not configured. API calls will fail until credentials are provided.');
  supabase = createClient('https://placeholder.supabase.co', 'placeholder-key');
}

export { supabase };

// ============================================
// Database Types (based on PHP MySQL schema)
// ============================================

export interface DbObject {
  id: number;
  pk_object_id: number;
  
  // Images (up to 10)
  filename1: string | null;
  filename2: string | null;
  filename3: string | null;
  filename4: string | null;
  filename5: string | null;
  filename6: string | null;
  filename7: string | null;
  filename8: string | null;
  filename9: string | null;
  filename10: string | null;
  
  // Cloudflare image IDs (for migration)
  cloudflare_image_id1: string | null;
  cloudflare_image_id2: string | null;
  cloudflare_image_id3: string | null;
  cloudflare_image_id4: string | null;
  cloudflare_image_id5: string | null;
  cloudflare_image_id6: string | null;
  cloudflare_image_id7: string | null;
  cloudflare_image_id8: string | null;
  cloudflare_image_id9: string | null;
  cloudflare_image_id10: string | null;
  
  // Core fields
  title: string | null;
  description: string | null;
  
  // Categories (up to 4)
  category1: string | null;
  category2: string | null;
  category3: string | null;
  category4: string | null;
  
  // Creator info
  creator: string | null;
  year: string | null;
  
  // Object details
  object_medium: string | null;
  object_dimensions: string | null;
  
  // Time and location
  time_period: string | null;
  nation: string | null;
  state: string | null;
  city: string | null;
  
  // Taxonomy (up to 4 sets)
  taxon_common_name: string | null;
  taxon_order: string | null;
  taxon_family: string | null;
  taxon_species: string | null;
  
  taxon_common_name2: string | null;
  taxon_order2: string | null;
  taxon_family2: string | null;
  taxon_species2: string | null;
  
  taxon_common_name3: string | null;
  taxon_order3: string | null;
  taxon_family3: string | null;
  taxon_species3: string | null;
  
  taxon_common_name4: string | null;
  taxon_order4: string | null;
  taxon_family4: string | null;
  taxon_species4: string | null;
  
  // Source info
  url: string | null;
  collection: string | null;
  citation: string | null;
  
  // Comments and metadata
  comments: string | null;
  permission_information: string | null;
  
  // Status fields
  hide: number;
  registered: number;
  
  // Submission info
  submit_first: string | null;
  submit_last: string | null;
  submit_email: string | null;
  
  // Timestamps
  entry_date: string | null;
  entry_update: string | null;
  created_at: string;
  updated_at: string;
}

export interface DbCategory {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  object_count: number;
}

export interface DbTaxonOrder {
  id: number;
  name: string;
  common_names: string[];
  object_count: number;
}

// ============================================
// Query Functions
// ============================================

/**
 * Fetch all objects with optional filters
 */
export async function getObjects(options?: {
  category?: string;
  taxonOrder?: string;
  nation?: string;
  page?: number;
  pageSize?: number;
}) {
  const page = options?.page || 1;
  const pageSize = options?.pageSize || 20;
  
  let query = supabase
    .from('objects')
    .select('*', { count: 'exact' })
    .eq('hide', 0)
    .eq('registered', 1)
    .order('pk_object_id', { ascending: true })
    .range((page - 1) * pageSize, page * pageSize - 1);
  
  if (options?.category) {
    // Escape special characters for PostgREST
    const escapedCategory = options.category.replace(/[()]/g, '');
    query = query.or(`category1.ilike.%${escapedCategory}%,category2.ilike.%${escapedCategory}%,category3.ilike.%${escapedCategory}%,category4.ilike.%${escapedCategory}%`);
  }
  
  if (options?.taxonOrder) {
    // Escape special characters for PostgREST
    const escapedTaxonOrder = options.taxonOrder.replace(/[()]/g, '');
    query = query.or(`taxon_order.ilike.%${escapedTaxonOrder}%,taxon_order2.ilike.%${escapedTaxonOrder}%,taxon_order3.ilike.%${escapedTaxonOrder}%,taxon_order4.ilike.%${escapedTaxonOrder}%`);
  }
  
  if (options?.nation) {
    query = query.ilike('nation', `%${options.nation}%`);
  }
  
  const { data, error, count } = await query;
  
  if (error) {
    console.error('Error fetching objects:', error);
    throw error;
  }
  
  return { data: data || [], total: count || 0, page, pageSize };
}

/**
 * Fetch a single object by ID
 */
export async function getObject(objectId: number) {
  const { data, error } = await supabase
    .from('objects')
    .select('*')
    .eq('pk_object_id', objectId)
    .eq('hide', 0)
    .eq('registered', 1)
    .single();
  
  if (error) {
    console.error('Error fetching object:', error);
    throw error;
  }
  
  return data;
}

/**
 * Parse search query with Google-style operators
 * +word = must contain
 * -word = must not contain
 * "phrase" = exact phrase
 * word = optional (OR)
 */
function parseSearchQuery(query: string): {
  required: string[];
  excluded: string[];
  phrases: string[];
  optional: string[];
} {
  const required: string[] = [];
  const excluded: string[] = [];
  const phrases: string[] = [];
  const optional: string[] = [];
  
  // Extract quoted phrases first
  const phraseRegex = /"([^"]+)"/g;
  let match;
  let remainingQuery = query;
  
  while ((match = phraseRegex.exec(query)) !== null) {
    const phrase = match[1].trim();
    if (phrase) {
      // Check if phrase is required or excluded
      const beforeQuote = query.substring(0, match.index).trim();
      if (beforeQuote.endsWith('+')) {
        required.push(phrase);
        remainingQuery = remainingQuery.replace(`+"${phrase}"`, ' ');
      } else if (beforeQuote.endsWith('-')) {
        excluded.push(phrase);
        remainingQuery = remainingQuery.replace(`-"${phrase}"`, ' ');
      } else {
        phrases.push(phrase);
        remainingQuery = remainingQuery.replace(`"${phrase}"`, ' ');
      }
    }
  }
  
  // Extract individual words with operators
  const words = remainingQuery.split(/\s+/).filter(w => w.trim());
  
  for (const word of words) {
    if (word.startsWith('+')) {
      const term = word.substring(1).trim();
      if (term) required.push(term);
    } else if (word.startsWith('-')) {
      const term = word.substring(1).trim();
      if (term) excluded.push(term);
    } else if (word.trim()) {
      optional.push(word.trim());
    }
  }
  
  return { required, excluded, phrases, optional };
}

/**
 * Search objects using full-text search with Boolean operators
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
) {
  const page = options?.page || 1;
  const pageSize = options?.pageSize || 20;
  
  // Parse search query
  const { required, excluded, phrases, optional } = parseSearchQuery(query);
  
  // If no search terms, return empty results
  if (required.length === 0 && excluded.length === 0 && phrases.length === 0 && optional.length === 0) {
    return {
      results: [],
      total: 0,
      page,
      pageSize,
      query
    };
  }
  
  try {
    // Build base query
    let dbQuery = supabase
      .from('objects')
      .select('*', { count: 'exact' })
      .eq('hide', 0)
      .eq('registered', 1);
    
    // Search fields
    const searchFields = [
      'title', 'description', 'category1', 'category2', 'category3',
      'creator', 'object_medium', 'time_period', 'nation', 'state', 'city',
      'taxon_common_name', 'taxon_order', 'taxon_family', 'taxon_species', 'collection'
    ];
    
    // Add required terms (must contain - AND)
    for (const term of [...required, ...phrases]) {
      const pattern = `%${term}%`;
      const orConditions = searchFields.map(field => `${field}.ilike.${pattern}`).join(',');
      dbQuery = dbQuery.or(orConditions);
    }
    
    // Add optional terms (OR - at least one must match if no required terms)
    if (optional.length > 0 && required.length === 0 && phrases.length === 0) {
      const allOptionalPatterns = optional.map(term => {
        const pattern = `%${term}%`;
        return searchFields.map(field => `${field}.ilike.${pattern}`).join(',');
      }).join(',');
      dbQuery = dbQuery.or(allOptionalPatterns);
    }
    
    // Add excluded terms (must NOT contain - NOT)
    // PostgREST doesn't support complex NOT OR easily, so we filter after fetch
    const hasExclusions = excluded.length > 0;
    
    // If we have exclusions, fetch more results to account for filtering
    const fetchSize = hasExclusions ? pageSize * 3 : pageSize;
    const fetchOffset = hasExclusions ? (page - 1) * fetchSize : (page - 1) * pageSize;
    
    // Apply order and pagination
    dbQuery = dbQuery
      .order('pk_object_id', { ascending: true })
      .range(fetchOffset, fetchOffset + fetchSize - 1);
    
    if (options?.category) {
      // Escape special characters for PostgREST
      const escapedCategory = options.category.replace(/[()]/g, '');
      dbQuery = dbQuery.or(`category1.ilike.%${escapedCategory}%,category2.ilike.%${escapedCategory}%`);
    }
    
    if (options?.taxonOrder) {
      // Escape special characters for PostgREST
      const escapedTaxonOrder = options.taxonOrder.replace(/[()]/g, '');
      dbQuery = dbQuery.or(`taxon_order.ilike.%${escapedTaxonOrder}%,taxon_order2.ilike.%${escapedTaxonOrder}%`);
    }
    
    if (options?.nation) {
      dbQuery = dbQuery.ilike('nation', `%${options.nation}%`);
    }
    
    const { data, error, count } = await dbQuery;
    
    if (error) {
      throw error;
    }
    
    // Filter out excluded terms in-memory
    let filteredData = data || [];
    if (excluded.length > 0) {
      filteredData = filteredData.filter(obj => {
        // Check if object contains any excluded term in any field
        for (const term of excluded) {
          const termLower = term.toLowerCase();
          for (const field of searchFields) {
            const value = (obj as any)[field];
            if (value && String(value).toLowerCase().includes(termLower)) {
              return false; // Exclude this object
            }
          }
        }
        return true; // Keep this object
      });
      
      // Paginate filtered results
      const start = (page - 1) * pageSize;
      filteredData = filteredData.slice(start, start + pageSize);
    }
    
    return {
      results: filteredData,
      total: filteredData.length,
      page,
      pageSize,
      query
    };
  } catch (error) {
    console.error('Search error:', error);
    throw error;
  }
}

/**
 * Get unique categories from all objects
 */
export async function getCategories() {
  const { data, error } = await supabase
    .from('objects')
    .select('category1, category2, category3, category4')
    .eq('hide', 0)
    .eq('registered', 1);
  
  if (error) {
    console.error('Error fetching categories:', error);
    throw error;
  }
  
  // Extract unique categories
  const categories = new Set<string>();
  data?.forEach(obj => {
    [obj.category1, obj.category2, obj.category3, obj.category4]
      .filter(Boolean)
      .forEach(cat => categories.add(cat!.trim()));
  });
  
  return Array.from(categories).sort();
}

/**
 * Get unique taxon orders from all objects
 */
export async function getTaxonOrders() {
  const { data, error } = await supabase
    .from('objects')
    .select('taxon_order, taxon_order2, taxon_order3, taxon_order4')
    .eq('hide', 0)
    .eq('registered', 1);
  
  if (error) {
    console.error('Error fetching taxon orders:', error);
    throw error;
  }
  
  // Extract unique orders
  const orders = new Set<string>();
  data?.forEach(obj => {
    [obj.taxon_order, obj.taxon_order2, obj.taxon_order3, obj.taxon_order4]
      .filter(Boolean)
      .forEach(order => orders.add(order!.trim()));
  });
  
  return Array.from(orders).sort();
}

/**
 * Get unique nations from all objects
 */
export async function getNations() {
  const { data, error } = await supabase
    .from('objects')
    .select('nation')
    .eq('hide', 0)
    .eq('registered', 1)
    .not('nation', 'is', null);
  
  if (error) {
    console.error('Error fetching nations:', error);
    throw error;
  }
  
  const nations = new Set<string>();
  data?.forEach(obj => {
    if (obj.nation?.trim()) {
      nations.add(obj.nation.trim());
    }
  });
  
  return Array.from(nations).sort();
}

/**
 * Submit a new object (contribution)
 */
export async function submitObject(object: Partial<DbObject>) {
  const { data, error } = await supabase
    .from('objects')
    .insert({
      ...object,
      hide: 0,
      registered: 0, // Needs approval
      entry_date: new Date().toISOString(),
    })
    .select()
    .single();
  
  if (error) {
    console.error('Error submitting object:', error);
    throw error;
  }
  
  return data;
}

