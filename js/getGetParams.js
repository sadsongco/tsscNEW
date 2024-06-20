const getBlogId = () => {
  return new URLSearchParams(window.location.search).get('blog_id');
};

document.body.addEventListener('blogLoaded', function (evt) {
  // alert(evt.detail.value);
});
