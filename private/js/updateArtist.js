const updateArtist = (e) => {
  const currOption = document.getElementById(e.target.value);
  const targetField = document.getElementById('clip_artist');
  targetField.value = currOption.dataset.artist;
};
