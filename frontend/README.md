# Cultural Entomology Database - Frontend

A modern Next.js frontend for the Cultural Entomology Database (Insects Incorporated), deployed on Cloudflare Pages with Supabase as the backend.

## Tech Stack

- **Framework**: Next.js 15 with React 18
- **Styling**: Tailwind CSS
- **Icons**: Lucide React
- **Database**: Supabase (PostgreSQL)
- **Images**: Cloudflare Images
- **Hosting**: Cloudflare Pages

## Getting Started

### Prerequisites

- Node.js 18+
- npm or yarn
- Supabase account and project
- Cloudflare account (for deployment)

### Installation

1. Clone the repository and navigate to the frontend directory:
   ```bash
   cd culturalentomology_frontend
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Copy the example environment file:
   ```bash
   cp .env.example .env.local
   ```

4. Configure your environment variables in `.env.local`:
   - `NEXT_PUBLIC_SUPABASE_URL`: Your Supabase project URL
   - `NEXT_PUBLIC_SUPABASE_ANON_KEY`: Your Supabase anonymous key
   - `NEXT_PUBLIC_CF_IMAGES_ACCOUNT`: Your Cloudflare Images account hash

### Development

Run the development server:
```bash
npm run dev
```

Open [http://localhost:3000](http://localhost:3000) in your browser.

### Building for Production

```bash
npm run build
```

### Deploying to Cloudflare Pages

1. Build for Cloudflare Pages:
   ```bash
   npm run pages:build
   ```

2. Deploy:
   ```bash
   npm run deploy
   ```

Or connect your repository to Cloudflare Pages for automatic deployments.

## Database Schema

The application expects a Supabase PostgreSQL database with an `objects` table containing:

### Core Fields
- `pk_object_id`: Primary key / object ID
- `title`: Object title
- `description`: Detailed description
- `category1-4`: Up to 4 category classifications
- `creator`: Artist/creator name
- `year`: Creation year
- `object_medium`: Material/medium
- `object_dimensions`: Size information

### Location
- `nation`: Country of origin
- `state`: State/province
- `city`: City

### Taxonomy (up to 4 insects)
- `taxon_common_name[2-4]`: Common name
- `taxon_order[2-4]`: Scientific order
- `taxon_family[2-4]`: Scientific family
- `taxon_species[2-4]`: Species name

### Images
- `filename1-10`: Image filenames
- `cloudflare_image_id1-10`: Cloudflare image IDs (for CDN delivery)

### Metadata
- `url`: External source URL
- `collection`: Collection name
- `citation`: Source citation
- `hide`: Visibility flag (0 = visible)
- `registered`: Approval status (1 = approved)

## Project Structure

```
src/
├── app/                    # Next.js App Router pages
│   ├── page.tsx           # Home page with search
│   ├── about/             # About page
│   ├── browse/            # Browse by category
│   ├── object/[id]/       # Object detail page
│   └── submit/            # Contribution form
├── components/
│   ├── layout/            # Header, Footer
│   ├── search/            # SearchBox
│   ├── objects/           # ObjectCard, ObjectGrid
│   ├── filters/           # FilterSidebar
│   └── ui/                # ImageWithFallback
└── lib/
    ├── api.ts             # API client
    ├── supabase.ts        # Supabase client & queries
    └── cloudflare-images.ts # Image URL helpers
```

## Features

- **Full-text search** with Boolean operators (+, -, "exact phrase")
- **Filter by category**, insect order, or country
- **Responsive design** for mobile and desktop
- **Image gallery** with lightbox
- **Contribution form** for user submissions
- **Cloudflare Images** integration for optimized delivery

## License

Apache 2.0 License - See LICENSE file for details.

## Credits

- **Database curator**: Barrett Klein, University of Wisconsin - La Crosse
- **Website development**: Arno Klein

