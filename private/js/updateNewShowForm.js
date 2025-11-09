const addDays = (date, days) => {
  var result = new Date(date);
  result.setDate(result.getDate() + days);
  return result.toISOString().substring(0, 10);
};

const updateNewShowButton = (event) => {
  const addShowButton = document.getElementById('addShow');
  let params = addShowButton.getAttribute('hx-vals');
  params = JSON.parse(params);
  let updated = false;
  for (const param of Object.entries(params)) {
    if (param == event.target.dataset.update) {
      params[param] = event.target.value;
      updated = true;
    }
  }
  if (!updated) params[event.target.dataset.update] = event.target.value;
  addShowButton.setAttribute('hx-vals', JSON.stringify(params));
};

const updateNewShowForm = (event) => {
  const updateList = document.querySelectorAll('.' + event.target.dataset.update);
  updateList.forEach((updateItem) => {
    updateItem.value = event.target.value;
    if (event.target.dataset.update == 'date') (event.target.value = event.target.value), 1;
  });
  updateNewShowButton(event);
};

const bandDateCountryEventListeners = () => {
  // attach event listeners
  const dateSelect = document.querySelector('#dateSelect');
  dateSelect.addEventListener('change', updateNewShowForm);

  const selects = document.querySelectorAll('select');
  selects.forEach(($select) => $select.addEventListener('change', updateNewShowForm));
};
