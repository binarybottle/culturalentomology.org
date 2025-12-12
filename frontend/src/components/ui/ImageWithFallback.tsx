'use client';

import { useState } from 'react';
import Image, { ImageProps } from 'next/image';

interface ImageWithFallbackProps extends Omit<ImageProps, 'onError'> {
  fallbackSrc?: string;
}

export default function ImageWithFallback({
  src,
  alt,
  fallbackSrc = '/images/placeholder.jpg',
  ...props
}: ImageWithFallbackProps) {
  const [error, setError] = useState(false);
  const [loading, setLoading] = useState(true);

  return (
    <div className="relative w-full h-full">
      {loading && (
        <div className="absolute inset-0 skeleton" />
      )}
      <Image
        src={error ? fallbackSrc : src}
        alt={alt}
        onError={() => setError(true)}
        onLoad={() => setLoading(false)}
        {...props}
      />
    </div>
  );
}

