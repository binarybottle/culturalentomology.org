# Cultural Entomology Database - Frontend

A modern Next.js frontend for the Cultural Entomology Database (Insects Incorporated), showcasing 500+ curated examples of insects in art, literature, film, and culture throughout history.

**Live Site**: https://culturalentomology.pages.dev

## Quick Deploy

```bash
cd /Users/arno/Software/culturalentomology.org/frontend
rm -rf .vercel .next
npm run pages:build
npm run deploy
```

If you get authentication errors, unset conflicting API tokens:
```bash
unset CF_API_TOKEN
unset CLOUDFLARE_API_TOKEN
npm run deploy
```

## Tech Stack

- **Framework**: Next.js 15 with React 18 & App Router
- **Styling**: Tailwind CSS with custom design system
- **Icons**: Lucide React
- **Database**: Supabase (PostgreSQL)
- **Images**: Cloudflare Images (1,545 images, ~500 matched to database)
- **Hosting**: Cloudflare Pages with Edge Runtime
- **Deployment**: Wrangler CLI with @cloudflare/next-on-pages

## Features

### Search & Discovery
- **Advanced Boolean search** with Google-style operators:
  - `+term` = must contain
  - `-term` = must not contain  
  - `"exact phrase"` = phrase match
  - `term1 term2` = contains either (OR)
- **Multi-field search** across title, description, creator, categories, taxonomy, location, and more
- **Real-time filtering** by category, insect order, and country
- **Responsive grid layout** with image thumbnails

### Content
- **534 objects** from 34 countries across 41 categories
- **498 objects with images** (88% coverage)
- Categories include art, literature, film, music, jewelry, textiles, and more
- Rich metadata including taxonomy, location, dimensions, citations

### Images
- **Cloudflare Images integration** for optimized delivery
- Automatic format conversion (WebP/AVIF for modern browsers)
- Multiple variants (public variant currently used)
- Graceful fallback to placeholder for objects without images

### User Interface
- **Modern, accessible design** with glass morphism effects
- **Mobile-first responsive layout**
- **Edge Runtime** for fast global delivery
- **Static generation** for About page
- **Server-side rendering** for dynamic content

## Database Setup

### Migration from MySQL

The project includes scripts for migrating from MySQL to Supabase PostgreSQL:

1. **Universal migration script** (`migrate_mysql_to_supabase.py`):
   - Parses MySQL dump files
   - Converts data types (tinyint→BOOLEAN, etc.)
   - Generates PostgreSQL schema with RLS policies
   - Exports data to CSV files
   - Creates import script

2. **Image sync script** (`sync_cloudflare_images.py`):
   - Lists all Cloudflare Images
   - Matches to database records by filename (case-insensitive)
   - Updates `cloudflare_image_id` columns in database
   - Handles both exact matches and ID prefix matching

### Database Schema

The `objects` table contains:

**Core Fields**
- `id`: Internal ID (auto-increment)
- `pk_object_id`: Public object ID (referenced in URLs)
- `title`: Object title
- `description`: Detailed description
- `creator`: Artist/creator name
- `year`: Creation year
- `object_medium`: Material/medium
- `object_dimensions`: Size

**Categories** (up to 4)
- `category1`, `category2`, `category3`, `category4`

**Location**
- `nation`, `state`, `city`

**Taxonomy** (up to 4 insects)
- `taxon_common_name[1-4]`: Common name (e.g., "honey bee")
- `taxon_order[1-4]`: Order (e.g., "Hymenoptera")
- `taxon_family[1-4]`: Family
- `taxon_species[1-4]`: Scientific name

**Images** (up to 10)
- `filename[1-10]`: Original filenames
- `cloudflare_image_id[1-10]`: Cloudflare image UUIDs

**Metadata**
- `url`: External source URL
- `collection`: Museum/collection name
- `citation`: Source citation
- `comments`, `permission_information`

**Status**
- `hide`: Visibility (0 = visible, 1 = hidden)
- `registered`: Approval (1 = approved, 0 = pending)

**Timestamps**
- `entry_date`, `entry_update`: Submission dates
- `created_at`, `updated_at`: Auto-maintained

### Row Level Security (RLS)

Current policies:
- Public read access for approved objects (`hide=0 AND registered=1`)
- Write access requires authentication (submissions feature currently disabled)

## Environment Variables

### Required for Build/Deploy

Create `.env.local` for local development:

```env
NEXT_PUBLIC_SUPABASE_URL=https://your-project.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=your-anon-key
NEXT_PUBLIC_CF_IMAGES_ACCOUNT=your-cf-images-hash
```

### Cloudflare Images Hash

The Cloudflare Images account hash (`8HA2zssW7KVTY84RTNfJgA`) is hardcoded in `src/lib/cloudflare-images.ts` because environment variables were not being picked up consistently during builds.

### Production Deployment

Environment variables are set in:
1. Cloudflare Pages dashboard (Runtime variables)
2. `.env.production` file (Build-time for local builds)
3. `wrangler.toml` (Backup configuration)

## Project Structure

```
frontend/
├── src/
│   ├── app/                    # Next.js App Router pages
│   │   ├── page.tsx           # Home/search page (Edge Runtime)
│   │   ├── about/             # About page (Static)
│   │   ├── browse/            # Browse by category (Edge Runtime)
│   │   ├── object/[id]/       # Object detail (Edge Runtime, dynamic)
│   │   └── submit/            # Contribution form (disabled in UI)
│   ├── components/
│   │   ├── layout/            # Header, Footer
│   │   ├── search/            # SearchBox with Boolean operators
│   │   ├── objects/           # ObjectCard, ObjectGrid
│   │   ├── filters/           # FilterSidebar
│   │   └── ui/                # ImageWithFallback, reusable UI
│   └── lib/
│       ├── api.ts             # Transforms DB types to frontend types
│       ├── supabase.ts        # Database queries with Boolean search
│       └── cloudflare-images.ts # Image URL generation
├── public/
│   ├── images/
│   │   └── placeholder.svg    # Fallback for missing images
│   └── _routes.json           # Cloudflare Pages routing config
├── next.config.js             # Next.js + Cloudflare Pages config
├── wrangler.toml              # Cloudflare deployment config
└── package.json
```

## Development

### Local Development

```bash
npm install
npm run dev
```

Open http://localhost:3000

### Testing Search

Try these Boolean search queries:
- `+silk -"silk-screen"` → Must have "silk", must not have "silk-screen"
- `butterfly moth` → Has "butterfly" OR "moth"  
- `+"honey bee" -killer` → Must have "honey bee" phrase, must not have "killer"

### Building

```bash
# Standard Next.js build
npm run build

# Cloudflare Pages build (includes @cloudflare/next-on-pages)
npm run pages:build
```

## Deployment

The site auto-deploys to Cloudflare Pages when pushing to the repository. For manual deployment:

```bash
npm run pages:build
npm run deploy
```

### Deployment Configuration

- **Platform**: Cloudflare Pages
- **Build command**: `npm run pages:build`
- **Build output directory**: `.vercel/output/static`
- **Node version**: 18.x
- **Framework**: Next.js (with @cloudflare/next-on-pages adapter)

## Image Management

### Uploading Images

```bash
# Upload all images from a directory
export CF_API_TOKEN="your-cloudflare-api-token"
python3 upload_to_cloudflare_images.py /path/to/images

# Sync Cloudflare Images to database
export SUPABASE_SERVICE_KEY="your-service-key"
python3 sync_cloudflare_images.py
```

### Current Status
- **1,545 images** in Cloudflare Images
- **498 objects** matched to database (88% of objects with filenames)
- **350 objects** missing images (IDs 1-683, never uploaded)
- **19 TIFF files** failed upload (Cloudflare doesn't accept TIFFs)

## Known Issues & Future Improvements

### Known Issues
- Submit/Contribute feature disabled in UI (database write permissions need review)
- 68 objects still missing images (need to locate original files)
- Some search exclusions may need broader result fetching for accuracy

### Potential Improvements
- Add proper image variants in Cloudflare (thumb, medium, large)
- Implement PostgreSQL full-text search with ranking
- Add image lightbox/gallery view
- Create admin panel for content moderation
- Add related objects/recommendations
- Implement caching layer (Redis/KV)

## License

Apache 2.0 License

## Credits

- **Database curator**: Barrett Klein, PhD - University of Wisconsin, La Crosse
- **Website development**: Arno Klein
- **Original concept**: Cultural Entomology research initiative

---

For questions or issues, contact: barrett@pupating.org

