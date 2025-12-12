/**
 * Cloudflare Images URL Helper for Cultural Entomology
 * 
 * Generates URLs for images stored in Cloudflare Images with various variants.
 * Supports fallback to local images during development.
 */

// Cloudflare Images account hash (get from dashboard)
const CF_ACCOUNT_HASH = process.env.NEXT_PUBLIC_CF_IMAGES_ACCOUNT || '';

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
  variant: ImageVariant = 'large',
  baseUrl?: string
): string {
  // Prefer Cloudflare Images if ID is available
  const cfUrl = getCloudflareUrl(cloudflareId, variant);
  if (cfUrl) {
    return cfUrl;
  }
  
  // Fallback to local images
  if (!localPath) {
    return '/images/placeholder.jpg';
  }
  
  const apiUrl = baseUrl || process.env.NEXT_PUBLIC_API_URL || '';
  
  // Choose the right directory based on variant
  const dir = variant === 'thumb' ? 'thumbs' : 'images';
  
  return `${apiUrl}/${dir}/${localPath}`;
}

/**
 * Get thumbnail URL for an object's primary image
 */
export function getThumbnailUrl(
  cloudflareId: string | null,
  localPath: string | null,
  baseUrl?: string
): string {
  return getImageUrl(cloudflareId, localPath, 'thumb', baseUrl);
}

/**
 * Get all image URLs for an object
 */
export function getObjectImageUrls(
  filenames: (string | null)[],
  cloudflareIds: (string | null)[],
  variant: ImageVariant = 'medium'
): string[] {
  const urls: string[] = [];
  
  for (let i = 0; i < filenames.length; i++) {
    const filename = filenames[i];
    const cfId = cloudflareIds[i] || null;
    
    if (filename || cfId) {
      urls.push(getImageUrl(cfId, filename, variant));
    }
  }
  
  return urls;
}

/**
 * Get placeholder image URL
 */
export function getPlaceholderUrl(): string {
  return '/images/placeholder.jpg';
}

