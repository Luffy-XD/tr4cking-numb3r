import { defineConfig } from 'vite';

export default defineConfig({
  root: 'resources',
  build: {
    outDir: '../public/assets',
    emptyOutDir: true
  }
});
