const removeShow = (e) => {
  const fieldsetToRemove = document.getElementById('showFieldSet_' + e.dataset.fieldset);
  fieldsetToRemove.remove();
  e.remove();
};
