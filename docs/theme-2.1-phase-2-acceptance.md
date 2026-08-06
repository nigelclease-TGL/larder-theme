# Theme 2.1 Phase 2 acceptance checklist

## Repository validation

- [ ] PHP syntax passes for every theme PHP file.
- [ ] JavaScript syntax passes for every theme JavaScript file.
- [ ] CSS structure validation passes.
- [ ] Theme ZIP builds and passes integrity testing.
- [ ] Existing Theme 2.1 product-platform contract remains intact.
- [ ] Recipe-product integration contract passes.

## WordPress administration

- [ ] Recipe posts show a Recommended Products panel.
- [ ] Multiple Kitchen Products can be linked to one recipe.
- [ ] Draft products can be selected by editors.
- [ ] Draft products remain hidden from public pages.
- [ ] Kitchen Products list shows a linked-recipe count.
- [ ] Product and recipe relationships survive saving and reopening.

## Block editor

- [ ] Recommended Products appears in the block inserter.
- [ ] Linked-product mode reads the recipe relationship.
- [ ] Manual-product mode permits a separate selection.
- [ ] Heading and introduction controls work.
- [ ] Retailer-button visibility control works.
- [ ] Dynamic preview renders without editing existing recipe content.

## Public recipe pages

- [ ] Existing recipes remain unchanged until the block or shortcode is inserted.
- [ ] Published linked products render in the saved order.
- [ ] Product images, brand, title and editorial excerpt render correctly.
- [ ] Internal product links work.
- [ ] Primary retailer links open safely with sponsored attributes.
- [ ] Affiliate disclosure wording appears beneath the cards.
- [ ] Layout is responsive on desktop, tablet and mobile.

## Public product pages

- [ ] Linked published recipes appear under Recipes using this product.
- [ ] Unlinked products remain unchanged.
- [ ] Draft or private recipes never appear publicly.

## Preservation checks

- [ ] WP Recipe Maker cards remain intact.
- [ ] Pinterest functionality remains intact.
- [ ] Ad Inserter integration remains intact.
- [ ] NKT GPT connector compatibility remains intact.
- [ ] Existing Shop My Kitchen archive and product templates remain intact.
- [ ] No product is automatically published or inserted into a recipe.

## Release boundary

The branch may be merged only after repository review and explicit approval. Nigel receives one cumulative WordPress theme ZIP at the end of the approved Theme 2.1 work rather than installing this intermediate development build.
