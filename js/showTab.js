const showTab = (event) => {
  const tabs = document.querySelectorAll('.tab');
  for (const tab of [...tabs]) {
    tab.addEventListener('transitionend', showHideTab);
  }
  const targetTabSelector = event.target;
  const targetTab = document.getElementById(targetTabSelector.dataset.tab);
  const currentTabSelector = document.querySelector('.activeTab');
  const currentTab = document.getElementById(currentTabSelector.dataset.tab);
  deactivateActiveTabSelector(currentTabSelector);
  activateTabSelector(targetTabSelector);
  hideContentTab(currentTab);
  showContentTab(targetTab);
};

const deactivateActiveTabSelector = (tab) => {
  tab.classList.remove('activeTab');
  tab.classList.add('inactiveTab');
};

const activateTabSelector = (tab) => {
  tab.classList.remove('inactiveTab');
  tab.classList.add('activeTab');
};

const showContentTab = (tab) => {
  tab.classList.remove('noDisplay');
  tab.classList.remove('hiddenTab');
  tab.classList.add('visibleTab');
};

const hideContentTab = (tab) => {
  tab.classList.remove('visibleTab');
  tab.classList.add('hiddenTab');
};

const showHideTab = (e) => {
  if (e.target.classList.contains('visibleTab')) return;
  e.target.classList.add('noDisplay');
};
