import Link from 'next/link';
import { Bug, BookOpen, Users, Globe, Mail, ExternalLink, ArrowRight } from 'lucide-react';

export const metadata = {
  title: 'About - Cultural Entomology Database',
  description: 'Learn about the Cultural Entomology Database and how to contribute.',
};

export default function AboutPage() {
  return (
    <div className="min-h-screen">
      {/* Hero */}
      <section className="hero-section py-16 md:py-24 relative">
        <div className="container mx-auto px-4 relative z-10">
          <div className="max-w-3xl mx-auto text-center">
            <h1 className="text-4xl md:text-5xl font-display font-bold text-surface-900 mb-6">
              About <span className="text-primary-600">Insects Incorporated</span>
            </h1>
            <p className="text-lg text-surface-600 leading-relaxed">
              The most comprehensive, manually-annotated cultural entomology database in the world.
            </p>
          </div>
        </div>
        <div className="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-surface-50 to-transparent" />
      </section>

      {/* How to Use */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto">
            <div className="flex items-center gap-3 mb-6">
              <div className="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center">
                <BookOpen className="w-6 h-6 text-primary-600" />
              </div>
              <h2 className="text-2xl font-display font-bold text-surface-800">
                How to Use the Database
              </h2>
            </div>
            
            <div className="prose prose-lg max-w-none text-surface-600">
              <p>
                On the home page, type words in the search box and click the "Search" button. 
                You can refine your search using Boolean search symbols:
              </p>
              
              <div className="bg-surface-50 rounded-xl p-6 my-6 not-prose">
                <h3 className="font-semibold text-surface-800 mb-4">Search Examples</h3>
                <ul className="space-y-3">
                  <li className="flex items-start gap-3">
                    <code className="px-3 py-1 bg-primary-100 text-primary-700 rounded-lg text-sm font-mono">
                      +silk -"silk-screen"
                    </code>
                    <span className="text-surface-600">
                      Objects with "silk" but not "silk-screen"
                    </span>
                  </li>
                  <li className="flex items-start gap-3">
                    <code className="px-3 py-1 bg-primary-100 text-primary-700 rounded-lg text-sm font-mono">
                      butterfly painting
                    </code>
                    <span className="text-surface-600">
                      Objects containing both words
                    </span>
                  </li>
                  <li className="flex items-start gap-3">
                    <code className="px-3 py-1 bg-primary-100 text-primary-700 rounded-lg text-sm font-mono">
                      "beetle jewelry"
                    </code>
                    <span className="text-surface-600">
                      Exact phrase match
                    </span>
                  </li>
                </ul>
              </div>
              
              <p>
                Use the filters on the left sidebar to narrow results by category, 
                insect order, or country of origin.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* What is Cultural Entomology */}
      <section className="py-16 bg-surface-50">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto">
            <div className="flex items-center gap-3 mb-6">
              <div className="w-12 h-12 rounded-xl bg-accent-100 flex items-center justify-center">
                <Bug className="w-6 h-6 text-accent-600" />
              </div>
              <h2 className="text-2xl font-display font-bold text-surface-800">
                What is Cultural Entomology?
              </h2>
            </div>
            
            <div className="prose prose-lg max-w-none text-surface-600 space-y-4">
              <blockquote className="border-l-4 border-primary-400 pl-6 italic text-surface-700">
                "Cultural entomology studies the reasons, beliefs, and symbolism behind the 
                inclusion of insects within all facets of the humanities."
                <footer className="text-sm text-surface-500 mt-2 not-italic">
                  — Dexter Sear, Cultural Entomology Digest, 1993
                </footer>
              </blockquote>
              
              <blockquote className="border-l-4 border-accent-400 pl-6 italic text-surface-700">
                "Cultural entomology is the branch of investigation that addresses the influence 
                of insects (and other terrestrial Arthropoda, including arachnids, myriapods, etc.) 
                in literature, language, music, the arts, interpretive history, religion, and recreation."
                <footer className="text-sm text-surface-500 mt-2 not-italic">
                  — Charles Hogue, CE Digest, 1993
                </footer>
              </blockquote>
              
              <p>
                Humans have included insects in art for millennia, either symbolically, literally, 
                or physically (insect products or bodies as art media). The practice of incorporating 
                insects in art is one of our primary cultural entomology interests.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* About the Project */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto">
            <div className="flex items-center gap-3 mb-6">
              <div className="w-12 h-12 rounded-xl bg-surface-200 flex items-center justify-center">
                <Globe className="w-6 h-6 text-surface-600" />
              </div>
              <h2 className="text-2xl font-display font-bold text-surface-800">
                About the Project
              </h2>
            </div>
            
            <div className="prose prose-lg max-w-none text-surface-600">
              <p>
                <strong>Barrett Klein</strong> is an Associate Professor of Entomology and Animal Behavior 
                at the University of Wisconsin - La Crosse. He heads the{' '}
                <a 
                  href="http://pupating.org" 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="text-primary-600 hover:text-primary-700"
                >
                  Pupating Lab
                </a>
                , which is building this searchable database of cultural entomology from Barrett Klein's 
                personal collection, from visitor submissions, and from numerous repositories on the Internet.
              </p>
              
              <p>
                Insects Incorporated is accepting submissions with the goal of establishing the most 
                comprehensive, manually-annotated cultural entomology database in the world. We anticipate 
                that it will be a valuable resource for research and for education at all levels, and 
                that it will also be of general interest to a wide audience on the internet.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Contributors */}
      <section className="py-16 bg-surface-50">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto">
            <div className="flex items-center gap-3 mb-8">
              <div className="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center">
                <Users className="w-6 h-6 text-primary-600" />
              </div>
              <h2 className="text-2xl font-display font-bold text-surface-800">
                Acknowledgments
              </h2>
            </div>
            
            <div className="prose prose-lg max-w-none text-surface-600">
              <p>
                <a 
                  href="https://www.binarybottle.com/" 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="text-primary-600 hover:text-primary-700"
                >
                  Arno Klein
                </a>
                , identical twin and champion for the wee, digital hexapod, made this website possible.
              </p>
              
              <p>
                UWL students contributing to the site: Cody Babcock, Abigail Reinke, Natalie Renier, 
                Rebecca Schnabel, Keaton Unrein, Danielle VanBrabant, and Breanna Vey.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Collage Image */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="max-w-4xl mx-auto">
            <div className="image-frame overflow-hidden rounded-2xl">
              <img
                src="/decor/Ethnoentomology_collage_web.jpg"
                alt="Cultural Entomology Collage"
                className="w-full h-auto"
              />
            </div>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-16 bg-gradient-to-br from-primary-600 to-primary-800 text-white">
        <div className="container mx-auto px-4">
          <div className="max-w-2xl mx-auto text-center">
            <h2 className="text-3xl font-display font-bold mb-4">
              Help Expand the Database
            </h2>
            <p className="text-primary-100 text-lg mb-8">
              You can contribute well-annotated examples of cultural entomology to help build 
              this valuable resource for researchers and enthusiasts worldwide.
            </p>
            {/* Contribute button hidden for now */}
            {/* <Link href="/submit" className="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary-700 rounded-xl font-semibold hover:bg-primary-50 transition-colors">
              Contribute to the Database
              <ArrowRight className="w-5 h-5" />
            </Link> */}
          </div>
        </div>
      </section>
    </div>
  );
}

