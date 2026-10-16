# Agent / developer instructions — Recupé Plus theme

**Before making any code, content, or configuration changes in this project, read:**

1. [`docs/PROJECT.md`](docs/PROJECT.md) — full project context (structure, templates, ACF, posts/categories, LP, build, TODOs)
2. Relevant files under [`.cursor/rules/`](.cursor/rules/) — placement, naming, Swiper, shortcodes
3. Relevant skill under [`.cursor/skills/`](.cursor/skills/) when creating PHP / shortcodes / Swiper

## Rules of engagement

- Do **not** invent product catalogs or replace the static Gomitas/Cápsulas slider until WooCommerce products exist (see PROJECT.md TODOs).
- Prefer ACF Options + helpers over hardcoding copy or asset URLs.
- Keep LP (`recupepluslp`) and this theme as separate repos; use the LP as a design/content reference.
- Escape/sanitize all dynamic output. Do not commit secrets or expose `wp-cache.php`-style tools.
- After SCSS/JS changes, recompile (`theme-styles.css` / `theme-scripts.js`) before claiming a visual fix.
- Ask before destructive DB or git operations (force push, hard reset, mass deletes).

If `docs/PROJECT.md` and the code disagree, trust the code, then update the doc in the same change.
