# Upgrade Notes

#### v2.0.0
Added `bool` as possible type to `$condition` parameter of `addCondition` function in `ProductListInterface` (and all implementing classes).

#### v1.2.1
- Removed the package "rybakit/twig-deferred-extension". If you extend the twig layout from the E-Commerce Framework, 
  please check if custom CSS/JS code added by `pimcore_head_script` and `pimcore_head_link` is still working. 

#### v2.0.0
- `AbstractOfferToolProduct` now only accepts int as the `$id` parameter.