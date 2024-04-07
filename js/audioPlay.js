document.addEventListener(
  'play',
  function (evt) {
    if (this.$AudioPlaying && this.$AudioPlaying !== evt.target) {
      this.$AudioPlaying.pause();
    }
    this.$AudioPlaying = evt.target;
  },
  true
);
