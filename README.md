# dropout-plugin

Simple recommendation plugin for WordPress. Used as toy example for a talk about [Machine Learning in WordPress](http://pieroit.github.io/machine-learning-open-course/index.html#/)

# How does it work?

The plugin computes similarity between two texts via [Jaccard/Tanimoto coefficient](https://en.wikipedia.org/wiki/Jaccard_index). Similarities are stored in `wp_postmeta` for each post; as a result WP stores a similarity graph that can be queried quickly to output recommendations (which is, the most similar posts to the one currently in page).

Two hooks are used:

- `save_post`: when a post is saved, it gets compared to the latest posts and results are saved in `wp_postmeta` as an array.
- `the_content`: when a post is showed in page, the recommended posts are extracted from the array in `wp_postmeta`.
