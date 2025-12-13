/** @type {import('next').NextConfig} */
const nextConfig = {
  images: {
    remotePatterns: [
      {
        protocol: 'https',
        hostname: 'imagedelivery.net',
      },
      {
        protocol: 'https',
        hostname: '*.supabase.co',
      },
    ],
    // Use unoptimized for Cloudflare Pages
    unoptimized: true,
  },
  // Don't use 'export' - next-on-pages handles this
  // output: 'export',
};

module.exports = nextConfig;

