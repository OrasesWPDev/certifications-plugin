# Certifications Plugin

A custom WordPress plugin for managing and displaying certifications with ACF integration.

## Development

This plugin uses Git for version control and GitHub Actions for automated deployment.

### Workflow

1. Create a feature branch from `develop`
2. Make your changes
3. Test thoroughly
4. Push to GitHub and create a PR to `develop`
5. Once approved, merge to `develop`
6. When ready to deploy, create a PR from `develop` to `main`
7. Merging to `main` will automatically:
   - Increment the version number
   - Deploy to the production server

### Requirements

- Advanced Custom Fields Pro
- WordPress 5.8+
- PHP 7.4+