/**
 * Cloudflare Images URL Helper for Cultural Entomology
 * 
 * Generates URLs for images stored in Cloudflare Images with various variants.
 * Supports fallback to local images during development.
 */

// Cloudflare Images account hash (hardcoded - DO NOT USE ENV VAR as it gets wrong value)
const CF_ACCOUNT_HASH = '8HA2zssW7KVTY84RTNfJgA';

// Image variants defined in Cloudflare Images dashboard
export type ImageVariant = 'thumb' | 'medium' | 'large' | 'public';

// Variant sizes (for reference when setting up Cloudflare dashboard)
export const VARIANT_SIZES: Record<ImageVariant, { width: number; fit: string }> = {
  thumb: { width: 200, fit: 'cover' },
  medium: { width: 480, fit: 'contain' },
  large: { width: 1200, fit: 'contain' },
  public: { width: 0, fit: 'contain' }, // Original size
};

/**
 * Get the Cloudflare Images URL for an image
 */
export function getCloudflareUrl(cloudflareId: string | null, variant: ImageVariant = 'large'): string | null {
  if (!cloudflareId || !CF_ACCOUNT_HASH) {
    return null;
  }
  
  return `https://imagedelivery.net/${CF_ACCOUNT_HASH}/${cloudflareId}/${variant}`;
}

/**
 * Get the image URL, preferring Cloudflare Images with fallback to local
 */
export function getImageUrl(
  cloudflareId: string | null,
  localPath: string | null,
  variant: ImageVariant = 'public',
  baseUrl?: string
): string {
  // Prefer Cloudflare Images if ID is available
  const cfUrl = getCloudflareUrl(cloudflareId, 'public'); // Always use 'public' variant for now
  if (cfUrl) {
    return cfUrl;
  }
  
  // If no Cloudflare ID, use placeholder instead of trying to load from non-existent server
  return '/images/placeholder.svg';
}

/**
 * Get thumbnail URL for an object's primary image
 */
export function getThumbnailUrl(
  cloudflareId: string | null,
  localPath: string | null,
  baseUrl?: string
): string {
  return getImageUrl(cloudflareId, localPath, 'public', baseUrl);
}

/**
 * Get all image URLs for an object
 */
export function getObjectImageUrls(
  filenames: (string | null)[],
  cloudflareIds: (string | null)[],
  variant: ImageVariant = 'public'
): string[] {
  const urls: string[] = [];
  
  for (let i = 0; i < filenames.length; i++) {
    const filename = filenames[i];
    const cfId = cloudflareIds[i] || null;
    
    if (filename || cfId) {
      urls.push(getImageUrl(cfId, filename, 'public'));
    }
  }
  
  return urls;
}

/**
 * Get placeholder image URL
 */
export function getPlaceholderUrl(): string {
  return '/images/placeholder.svg';
}

