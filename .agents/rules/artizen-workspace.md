---
trigger: always_on
---

# Artizen Workspace Rule

## Project Context
Artizen is a premium Event Booking Platform built with Laravel and Bootstrap 5. The platform allows customers to browse and book complete event packages for celebrations such as birthdays, anniversaries, proposals, house parties, baby showers, weddings, and corporate events.

Primary launch city is Indore, Madhya Pradesh. The architecture must support adding multiple cities in the future.

## Business Rules
- This is an Event Booking Platform, not a traditional e-commerce website.
- Customers submit booking requests only.
- Do NOT implement any online payment gateway.
- Payment is collected offline after the booking is confirmed by the admin.
- Every booking must be stored in the database and manageable from the admin panel.

## Booking Flow
Browse Packages → Package Details → Book Now → Booking Form → Save Booking → Admin Review → Customer Contact → Booking Confirmation → Offline Payment

## Booking Form
Always collect:

Customer Information
- Full Name
- Mobile Number
- WhatsApp Number
- Email Address

Event Information
- Event Type
- Selected Package
- Event Date
- Event Time
- Guest Count

Venue Information
- Full Address
- Area
- Landmark
- City
- State
- Pincode

Additional Notes

## UI & UX
Always create premium-quality UI.

Design Style:
- Modern
- Minimal
- Elegant
- Mobile First
- Responsive
- Fast Loading

Use Bootstrap 5 components.

Reference Inspiration:
- Airbnb
- Apple
- Urban Company
- Ferns N Petals
- BookMyShow

Maintain consistent spacing, typography, buttons, cards, colors, icons, and animations.

## Development Rules
- Use Laravel latest stable version.
- Follow MVC architecture.
- Keep controllers thin.
- Move business logic to Service classes when appropriate.
- Use Form Requests for validation.
- Use Eloquent ORM and relationships.
- Use Blade Components.
- Reuse existing components.
- Follow PSR-12 coding standards.
- Write modular and maintainable code.

## Security
- Validate every request.
- Prevent SQL Injection.
- Prevent XSS.
- Prevent CSRF.
- Protect admin routes.
- Validate uploaded files.
- Never expose sensitive information.

## Performance
- Optimize database queries.
- Prevent N+1 queries.
- Use eager loading.
- Optimize images.
- Lazy load heavy content.

## Admin Panel
Create fully dynamic modules for:
- Dashboard
- Categories
- Packages
- Bookings
- Customers
- Gallery
- Testimonials
- FAQs
- CMS Pages
- Contact Enquiries
- Website Settings
- SEO
- Roles & Permissions

## SEO
Support:
- Meta Title
- Meta Description
- Keywords
- Canonical URL
- Open Graph
- Dynamic Slugs
- XML Sitemap

## Deployment
Generate production-ready code only.
- No debug code.
- No hardcoded URLs.
- No hardcoded credentials.
- Use environment variables.
- Compatible with shared hosting and VPS.

## AI Behaviour
Before implementing any feature:
- Analyze the existing project structure.
- Reuse existing code whenever possible.
- Never break existing functionality.
- Maintain consistent coding style.
- Ensure responsive design.
- Verify security and performance before completing the task.