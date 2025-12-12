/**
 * Root layout component for Cultural Entomology Database
 * 
 * Defines the HTML structure, metadata, and global layout including
 * navigation header and responsive container.
 */

import type { Metadata } from 'next';
import './globals.css';
import Header from '@/components/layout/Header';
import Footer from '@/components/layout/Footer';

export const metadata: Metadata = {
  title: 'Insects Incorporated - Cultural Entomology Database',
  description: 'A searchable database of cultural entomology: insects in art, literature, music, religion, and more.',
  keywords: ['cultural entomology', 'insects', 'art', 'ethnoentomology', 'entomology', 'insect art', 'butterfly art', 'beetle art'],
  authors: [{ name: 'Barrett Klein' }],
  openGraph: {
    title: 'Insects Incorporated - Cultural Entomology Database',
    description: 'Explore the influence of insects in human culture throughout history.',
    type: 'website',
  },
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en">
      <body className="min-h-screen flex flex-col grain">
        <Header />
        <main className="flex-1">
          {children}
        </main>
        <Footer />
      </body>
    </html>
  );
}

