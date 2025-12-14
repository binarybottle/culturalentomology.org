import Link from 'next/link';
import { Bug, Mail, ExternalLink } from 'lucide-react';

export default function Footer() {
  return (
    <footer className="bg-surface-900 text-surface-200 mt-auto">
      {/* Main footer content */}
      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          {/* Brand column */}
          <div className="md:col-span-2">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-10 h-10 rounded-full bg-primary-600 flex items-center justify-center">
                <Bug className="w-5 h-5 text-white" />
              </div>
              <span className="text-xl font-bold text-white font-display">
                Insects Incorporated
              </span>
            </div>
            <p className="text-surface-400 text-sm leading-relaxed max-w-md">
              A searchable database of cultural entomology—exploring the profound influence 
              of insects on art, literature, music, religion, and human culture throughout history.
            </p>
          </div>

          {/* Quick links */}
          <div>
            <h3 className="text-white font-semibold mb-4">Explore</h3>
            <ul className="space-y-2 text-sm">
              <li>
                <Link href="/" className="hover:text-primary-400 transition-colors">
                  Search Database
                </Link>
              </li>
              <li>
                <Link href="/browse" className="hover:text-primary-400 transition-colors">
                  Browse Collection
                </Link>
              </li>
              <li>
                <Link href="/about" className="hover:text-primary-400 transition-colors">
                  About the Project
                </Link>
              </li>
              {/* Contribute link hidden for now */}
              {/* <li>
                <Link href="/submit" className="hover:text-primary-400 transition-colors">
                  Contribute
                </Link>
              </li> */}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h3 className="text-white font-semibold mb-4">Contact</h3>
            <ul className="space-y-2 text-sm">
              <li className="flex items-center gap-2">
                <Mail className="w-4 h-4" />
                <a 
                  href="mailto:barrett@pupating.org" 
                  className="hover:text-primary-400 transition-colors"
                >
                  barrett@pupating.org
                </a>
              </li>
              <li className="flex items-center gap-2">
                <ExternalLink className="w-4 h-4" />
                <a 
                  href="http://pupating.org" 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="hover:text-primary-400 transition-colors"
                >
                  Pupating Lab
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      {/* Bottom bar */}
      <div className="border-t border-surface-800">
        <div className="container mx-auto px-4 py-4">
          <div className="flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-surface-500">
            <p>
              © {new Date().getFullYear()} Barrett Klein, University of Wisconsin - La Crosse. 
              Website by{' '}
              <a 
                href="https://www.binarybottle.com" 
                target="_blank" 
                rel="noopener noreferrer"
                className="hover:text-primary-400 transition-colors"
              >
                Arno Klein
              </a>
              .
            </p>
            <p>
              Built with Next.js, Supabase, and Cloudflare
            </p>
          </div>
        </div>
      </div>
    </footer>
  );
}

