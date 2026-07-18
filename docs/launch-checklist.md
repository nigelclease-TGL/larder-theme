# Nigel's Kitchen Table — Launch Checklist

Use this checklist on a staging copy of `thegourmetlarder.com`. Do not activate the theme on the live site until every critical item is complete.

## 1. Before installation

- [ ] Run a full UpdraftPlus backup of database, plugins, themes and uploads.
- [ ] Confirm the backup can be downloaded or restored.
- [ ] Confirm staging is blocked from search indexing.
- [ ] Record the current active theme: Kale Child.
- [ ] Export or screenshot the current menus and widget assignments.
- [ ] Confirm the current permalink structure and do not change it.

## 2. Install the theme

- [ ] Download the latest `nigels-kitchen-table.zip` GitHub Actions artifact.
- [ ] Upload under **Appearance → Themes → Add New → Upload Theme**.
- [ ] Activate only on staging.
- [ ] Open **Appearance → Kitchen Table Setup**.
- [ ] Run **Create missing pages and set homepage**.
- [ ] Confirm the homepage, Recipes, Recipe Collections, Kitchen Notes, About Nigel and Contact pages resolve correctly.
- [ ] Open **Tools → Site Health** and resolve every critical Nigel’s Kitchen Table item after activation.

## 3. Brand and media

- [ ] Upload the ten selected recipe photographs through **Media → Add New**.
- [ ] Add the approved alt text from `docs/photo-asset-plan.md`.
- [ ] Select the mango and passion fruit cheesecake as the homepage hero.
- [ ] Select Nigel's portrait as the small About image.
- [ ] Confirm the portrait remains secondary on desktop and mobile.
- [ ] Set the NKT monogram as the WordPress Site Icon, or retain the bundled fallback icon.
- [ ] Regenerate thumbnails after the theme is active.

## 4. Navigation and content structure

- [ ] Assign a Primary Menu containing Recipes, Collections, Kitchen Notes, About Nigel and Contact.
- [ ] Assign a simplified Footer Menu.
- [ ] Remove X and RSS from visible social navigation unless they are actively used.
- [ ] Confirm all old recipe URLs remain unchanged.
- [ ] Confirm legacy collection pages still open or redirect intentionally.
- [ ] Check category archives for Cakes, Biscuits & Cookies, Brownies & Bars, Bread, Pastry, Desserts & Puddings, Ice Cream and Breakfast.
- [ ] Open and close mobile navigation using keyboard only; confirm focus returns to the Menu button.
- [ ] Open and close recipe search using keyboard only; confirm focus returns to the Search button.

## 5. Plugin integration

- [ ] WP Recipe Maker recipe cards display correctly.
- [ ] Jump to Recipe reaches the recipe card on representative posts.
- [ ] WP Recipe Maker print view is clean and readable.
- [ ] Recipe ratings and structured data remain enabled.
- [ ] The chosen SEO plugin outputs titles, descriptions, canonical URLs and XML sitemaps correctly.
- [ ] Contact Form 7 sends a successful test message.
- [ ] Mailchimp for WordPress is connected to the correct audience.
- [ ] Enter the numeric MC4WP form ID in the theme Customizer.
- [ ] Complete a real newsletter test using a fresh email address and confirm the opt-in email.

## 6. Representative recipe QA

Test at least ten recipes, including:

- [ ] Mango and passion fruit cheesecake
- [ ] Chocolate fudge cake
- [ ] Carrot cake
- [ ] Cheesy pesto bread
- [ ] Cherry bars
- [ ] Chocolate crinkle cookies
- [ ] Biscoff biscuits
- [ ] Strawberry chiffon cake
- [ ] Viennese biscuits
- [ ] Artisan bread

For each recipe confirm:

- [ ] Featured image is sharp and correctly cropped.
- [ ] Title and introduction are readable.
- [ ] Ingredients and instructions display completely.
- [ ] Timings and servings are present.
- [ ] Print works.
- [ ] Cook Mode stays active and the screen remains awake where supported.
- [ ] Ingredient checkboxes work with mouse, touch and keyboard.
- [ ] Social sharing opens or copies the correct recipe URL.
- [ ] Related recipes are relevant.
- [ ] Comments and ratings work where enabled.

## 7. Device and browser QA

- [ ] iPhone-size viewport
- [ ] Android-size viewport
- [ ] Tablet portrait and landscape
- [ ] Desktop at 1366px
- [ ] Large desktop at 1920px
- [ ] Chrome
- [ ] Safari
- [ ] Firefox
- [ ] Edge
- [ ] Keyboard-only navigation
- [ ] Visible focus states
- [ ] Reduced-motion preference
- [ ] Browser back/forward navigation after applying recipe filters

## 8. Performance and accessibility

- [ ] Hero image is not stretched beyond its native dimensions.
- [ ] Images below the fold lazy-load.
- [ ] Homepage does not shift noticeably while loading.
- [ ] No browser console errors.
- [ ] No broken image or font requests.
- [ ] Colour contrast is acceptable.
- [ ] Every meaningful image has useful alt text.
- [ ] Heading order is logical.
- [ ] Forms have visible labels or accessible names.
- [ ] Search and menu dialogs do not allow keyboard focus behind them.
- [ ] Clear WP Super Cache and retest logged out.

## 9. Privacy, SEO and analytics

- [ ] WordPress Privacy Policy page is assigned.
- [ ] Cookie/consent configuration is reviewed for Analytics, advertising, embedded media and Mailchimp.
- [ ] Staging remains noindex until launch.
- [ ] Production remains indexable after launch.
- [ ] Existing Google Analytics / Site Kit tracking still fires after consent.
- [ ] SEO sitemap returns successfully.
- [ ] Recipe schema validates on representative recipes.
- [ ] Open Graph images and titles are correct.
- [ ] Pinterest sharing uses the intended portrait image.
- [ ] No unintended URL changes or redirect chains.

## 10. Go-live

- [ ] Take a fresh production backup immediately before activation.
- [ ] Put the live site in a brief maintenance window if required.
- [ ] Upload and activate the exact ZIP tested on staging.
- [ ] Repeat the setup checklist without changing permalinks.
- [ ] Assign menus and Customizer images.
- [ ] Enter the Mailchimp form ID.
- [ ] Regenerate thumbnails if production does not already have them.
- [ ] Confirm **Settings → Reading → Search engine visibility** allows indexing on production.
- [ ] Clear all caches.
- [ ] Test homepage, Recipes, one collection, About, Contact and five recipes while logged out.
- [ ] Confirm the old Kale Child theme remains installed for immediate rollback.

## 11. First 48 hours

- [ ] Monitor Contact Form 7 and Mailchimp submissions.
- [ ] Check analytics traffic and page views.
- [ ] Check Search Console for crawl or structured-data errors.
- [ ] Review 404 logs and repair any genuine broken links.
- [ ] Check mobile Core Web Vitals.
- [ ] Keep the pre-launch backup until the site has been stable for at least one week.
