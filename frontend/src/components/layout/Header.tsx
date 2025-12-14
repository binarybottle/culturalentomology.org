'use client';

import Link from 'next/link';
import { useState } from 'react';
import { Menu, X, Bug, Search } from 'lucide-react';

export default function Header() {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  return (
    <header className="sticky top-0 z-50 glass border-b border-surface-200/50">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-16 md:h-20">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-3 group">
            <div className="w-10 h-10 md:w-12 md:h-12 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
              <Bug className="w-5 h-5 md:w-6 md:h-6 text-white" />
            </div>
            <div className="flex flex-col">
              <span className="text-lg md:text-xl font-bold text-surface-800 font-display leading-tight">
                Insects Incorporated
              </span>
              <span className="text-xs text-surface-500 hidden sm:block">
                Cultural Entomology Database
              </span>
            </div>
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex items-center gap-1">
            <Link href="/" className="nav-link">
              Search
            </Link>
            <Link href="/browse" className="nav-link">
              Browse
            </Link>
            <Link href="/about" className="nav-link">
              About
            </Link>
            {/* Contribute link hidden for now */}
            {/* <Link href="/submit" className="nav-link">
              Contribute
            </Link> */}
          </nav>

          {/* Mobile menu button */}
          <button
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            className="md:hidden p-2 rounded-lg hover:bg-surface-100 transition-colors"
            aria-label="Toggle menu"
          >
            {mobileMenuOpen ? (
              <X className="w-6 h-6 text-surface-600" />
            ) : (
              <Menu className="w-6 h-6 text-surface-600" />
            )}
          </button>
        </div>

        {/* Mobile Navigation */}
        {mobileMenuOpen && (
          <nav className="md:hidden py-4 border-t border-surface-200/50 fade-in">
            <div className="flex flex-col gap-2">
              <Link
                href="/"
                className="px-4 py-3 rounded-lg hover:bg-surface-100 text-surface-700 font-medium transition-colors"
                onClick={() => setMobileMenuOpen(false)}
              >
                Search
              </Link>
              <Link
                href="/browse"
                className="px-4 py-3 rounded-lg hover:bg-surface-100 text-surface-700 font-medium transition-colors"
                onClick={() => setMobileMenuOpen(false)}
              >
                Browse
              </Link>
              <Link
                href="/about"
                className="px-4 py-3 rounded-lg hover:bg-surface-100 text-surface-700 font-medium transition-colors"
                onClick={() => setMobileMenuOpen(false)}
              >
                About
              </Link>
              {/* Contribute button hidden for now */}
              {/* <Link
                href="/submit"
                className="px-4 py-3 rounded-lg bg-primary-600 text-white font-medium text-center transition-colors"
                onClick={() => setMobileMenuOpen(false)}
              >
                Contribute
              </Link> */}
            </div>
          </nav>
        )}
      </div>
    </header>
  );
}

