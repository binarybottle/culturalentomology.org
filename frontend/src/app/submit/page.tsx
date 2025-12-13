'use client';

import { useState } from 'react';
import Link from 'next/link';
import { 
  Bug, 
  Send, 
  CheckCircle, 
  AlertCircle, 
  ArrowLeft,
  User,
  Mail,
  FileText,
  Tag,
  MapPin,
  Calendar,
  Link as LinkIcon
} from 'lucide-react';

export const runtime = 'edge';
import { submitContribution } from '@/lib/api';

interface FormData {
  submit_first: string;
  submit_last: string;
  submit_email: string;
  title: string;
  description: string;
  category1: string;
  creator: string;
  year: string;
  object_medium: string;
  nation: string;
  taxon_common_name: string;
  taxon_order: string;
  url: string;
}

const INITIAL_FORM: FormData = {
  submit_first: '',
  submit_last: '',
  submit_email: '',
  title: '',
  description: '',
  category1: '',
  creator: '',
  year: '',
  object_medium: '',
  nation: '',
  taxon_common_name: '',
  taxon_order: '',
  url: '',
};

const CATEGORIES = [
  'Painting', 'Sculpture', 'Drawing', 'Print', 'Photograph',
  'Textile', 'Jewelry', 'Ceramic', 'Glass', 'Metal',
  'Literature', 'Poetry', 'Music', 'Film', 'Animation',
  'Folk Art', 'Decorative Art', 'Commercial Art', 'Scientific Illustration',
  'Architecture', 'Design', 'Costume', 'Food', 'Other'
];

const INSECT_ORDERS = [
  'Coleoptera (beetles)', 'Lepidoptera (butterflies, moths)', 'Hymenoptera (bees, wasps, ants)',
  'Diptera (flies)', 'Orthoptera (grasshoppers, crickets)', 'Odonata (dragonflies, damselflies)',
  'Hemiptera (true bugs)', 'Mantodea (mantises)', 'Blattodea (cockroaches)',
  'Isoptera (termites)', 'Phasmatodea (stick insects)', 'Neuroptera (lacewings)',
  'Siphonaptera (fleas)', 'Phthiraptera (lice)', 'Other/Unknown'
];

export default function SubmitPage() {
  const [formData, setFormData] = useState<FormData>(INITIAL_FORM);
  const [submitting, setSubmitting] = useState(false);
  const [submitted, setSubmitted] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    setError(null);

    // Basic validation
    if (!formData.submit_first || !formData.submit_last || !formData.submit_email) {
      setError('Please fill in your name and email address.');
      setSubmitting(false);
      return;
    }

    if (!formData.title) {
      setError('Please provide a title for the object.');
      setSubmitting(false);
      return;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.submit_email)) {
      setError('Please enter a valid email address.');
      setSubmitting(false);
      return;
    }

    try {
      await submitContribution(formData);
      setSubmitted(true);
    } catch (err) {
      console.error('Submission error:', err);
      setError('There was an error submitting your contribution. Please try again.');
    } finally {
      setSubmitting(false);
    }
  };

  if (submitted) {
    return (
      <div className="min-h-screen bg-surface-50 py-16">
        <div className="container mx-auto px-4">
          <div className="max-w-lg mx-auto text-center">
            <div className="w-20 h-20 mx-auto mb-6 rounded-full bg-accent-100 flex items-center justify-center">
              <CheckCircle className="w-10 h-10 text-accent-600" />
            </div>
            <h1 className="text-3xl font-display font-bold text-surface-900 mb-4">
              Thank You!
            </h1>
            <p className="text-lg text-surface-600 mb-8">
              Your contribution has been submitted successfully. Our team will review it 
              and add it to the database if approved.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link href="/" className="btn-primary">
                Search the Database
              </Link>
              <button
                onClick={() => {
                  setSubmitted(false);
                  setFormData(INITIAL_FORM);
                }}
                className="btn-secondary"
              >
                Submit Another
              </button>
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-surface-50">
      {/* Header */}
      <div className="bg-white border-b border-surface-200">
        <div className="container mx-auto px-4 py-4">
          <Link 
            href="/" 
            className="inline-flex items-center gap-2 text-surface-500 hover:text-primary-600 transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Back to Search
          </Link>
        </div>
      </div>

      {/* Hero */}
      <section className="hero-section py-12 relative">
        <div className="container mx-auto px-4 relative z-10">
          <div className="max-w-2xl mx-auto text-center">
            <div className="w-16 h-16 mx-auto mb-6 rounded-full bg-primary-100 flex items-center justify-center">
              <Bug className="w-8 h-8 text-primary-600" />
            </div>
            <h1 className="text-3xl md:text-4xl font-display font-bold text-surface-900 mb-4">
              Contribute to the Database
            </h1>
            <p className="text-lg text-surface-600">
              Help us expand the collection with well-annotated examples of cultural entomology. 
              All submissions are reviewed before being added.
            </p>
          </div>
        </div>
        <div className="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-surface-50 to-transparent" />
      </section>

      {/* Form */}
      <section className="py-8">
        <div className="container mx-auto px-4">
          <form onSubmit={handleSubmit} className="max-w-2xl mx-auto space-y-8">
            {/* Error message */}
            {error && (
              <div className="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                <AlertCircle className="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
                <p className="text-red-700">{error}</p>
              </div>
            )}

            {/* Contributor Information */}
            <div className="bg-white rounded-xl p-6 shadow-sm border border-surface-200">
              <h2 className="text-lg font-display font-semibold text-surface-800 mb-6 flex items-center gap-2">
                <User className="w-5 h-5 text-primary-600" />
                Your Information
              </h2>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-surface-700 mb-2">
                    First Name <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    name="submit_first"
                    value={formData.submit_first}
                    onChange={handleChange}
                    className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all"
                    required
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-surface-700 mb-2">
                    Last Name <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    name="submit_last"
                    value={formData.submit_last}
                    onChange={handleChange}
                    className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all"
                    required
                  />
                </div>
              </div>
              
              <div className="mt-4">
                <label className="block text-sm font-medium text-surface-700 mb-2">
                  <Mail className="w-4 h-4 inline mr-1" />
                  Email Address <span className="text-red-500">*</span>
                </label>
                <input
                  type="email"
                  name="submit_email"
                  value={formData.submit_email}
                  onChange={handleChange}
                  className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all"
                  required
                />
              </div>
            </div>

            {/* Object Information */}
            <div className="bg-white rounded-xl p-6 shadow-sm border border-surface-200">
              <h2 className="text-lg font-display font-semibold text-surface-800 mb-6 flex items-center gap-2">
                <FileText className="w-5 h-5 text-primary-600" />
                Object Information
              </h2>
              
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-surface-700 mb-2">
                    Title <span className="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    name="title"
                    value={formData.title}
                    onChange={handleChange}
                    placeholder="e.g., Butterfly Brooch, Scarab Amulet, etc."
                    className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all"
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-surface-700 mb-2">
                    Description
                  </label>
                  <textarea
                    name="description"
                    value={formData.description}
                    onChange={handleChange}
                    rows={4}
                    placeholder="Describe the object and how insects are represented..."
                    className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all resize-none"
                  />
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-surface-700 mb-2">
                      <Tag className="w-4 h-4 inline mr-1" />
                      Category
                    </label>
                    <select
                      name="category1"
                      value={formData.category1}
                      onChange={handleChange}
                      className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all bg-white"
                    >
                      <option value="">Select a category...</option>
                      {CATEGORIES.map(cat => (
                        <option key={cat} value={cat}>{cat}</option>
                      ))}
                    </select>
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-surface-700 mb-2">
                      Medium
                    </label>
                    <input
                      type="text"
                      name="object_medium"
                      value={formData.object_medium}
                      onChange={handleChange}
                      placeholder="e.g., Oil on canvas, Bronze, etc."
                      className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-surface-700 mb-2">
                      Creator/Artist
                    </label>
                    <input
                      type="text"
                      name="creator"
                      value={formData.creator}
                      onChange={handleChange}
                      placeholder="Artist or creator name"
                      className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all"
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-surface-700 mb-2">
                      <Calendar className="w-4 h-4 inline mr-1" />
                      Year
                    </label>
                    <input
                      type="text"
                      name="year"
                      value={formData.year}
                      onChange={handleChange}
                      placeholder="e.g., 1850, ca. 1900, etc."
                      className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-sm font-medium text-surface-700 mb-2">
                    <MapPin className="w-4 h-4 inline mr-1" />
                    Country of Origin
                  </label>
                  <input
                    type="text"
                    name="nation"
                    value={formData.nation}
                    onChange={handleChange}
                    placeholder="e.g., France, Japan, Egypt, etc."
                    className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all"
                  />
                </div>
              </div>
            </div>

            {/* Insect Information */}
            <div className="bg-white rounded-xl p-6 shadow-sm border border-surface-200">
              <h2 className="text-lg font-display font-semibold text-surface-800 mb-6 flex items-center gap-2">
                <Bug className="w-5 h-5 text-accent-600" />
                Insect Information
              </h2>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-surface-700 mb-2">
                    Common Name
                  </label>
                  <input
                    type="text"
                    name="taxon_common_name"
                    value={formData.taxon_common_name}
                    onChange={handleChange}
                    placeholder="e.g., Butterfly, Beetle, Bee, etc."
                    className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all"
                  />
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-surface-700 mb-2">
                    Insect Order
                  </label>
                  <select
                    name="taxon_order"
                    value={formData.taxon_order}
                    onChange={handleChange}
                    className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all bg-white"
                  >
                    <option value="">Select an order...</option>
                    {INSECT_ORDERS.map(order => (
                      <option key={order} value={order.split(' ')[0]}>{order}</option>
                    ))}
                  </select>
                </div>
              </div>
            </div>

            {/* Source */}
            <div className="bg-white rounded-xl p-6 shadow-sm border border-surface-200">
              <h2 className="text-lg font-display font-semibold text-surface-800 mb-6 flex items-center gap-2">
                <LinkIcon className="w-5 h-5 text-primary-600" />
                Source
              </h2>
              
              <div>
                <label className="block text-sm font-medium text-surface-700 mb-2">
                  URL (if available online)
                </label>
                <input
                  type="url"
                  name="url"
                  value={formData.url}
                  onChange={handleChange}
                  placeholder="https://..."
                  className="w-full px-4 py-3 rounded-lg border border-surface-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-100 outline-none transition-all"
                />
              </div>
            </div>

            {/* Submit button */}
            <div className="flex flex-col sm:flex-row gap-4 justify-end">
              <Link href="/" className="btn-secondary">
                Cancel
              </Link>
              <button
                type="submit"
                disabled={submitting}
                className="btn-primary disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {submitting ? (
                  <>
                    <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2" />
                    Submitting...
                  </>
                ) : (
                  <>
                    <Send className="w-4 h-4 mr-2" />
                    Submit Contribution
                  </>
                )}
              </button>
            </div>
          </form>
        </div>
      </section>
    </div>
  );
}

