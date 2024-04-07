const showTab = (event) => {
  // const tabs = document.querySelectorAll('.tab');
  // for (const tab of [...tabs]) {
  //   tab.addEventListener('transitionend', showHideTab);
  // }
  const targetTabSelector = event.target;
  const targetTab = document.getElementById(targetTabSelector.dataset.tab);
  const currentTabSelector = document.querySelector('.activeTab');
  const currentTab = document.getElementById(currentTabSelector.dataset.tab);
  deactivateActiveTabSelector(currentTabSelector);
  activateTabSelector(targetTabSelector);
  hideContentTab(currentTab);
  showContentTab(targetTab);
  // const currentTab = document.querySelector('.visibleTab');
  // if (currentTab) {
  //   const currentTabSelectorId = currentTab.id + 'Tab';
  //   const currentSelector = document.getElementById(currentTabSelectorId);
  //   currentSelector.classList.remove('activeTab');
  //   currentSelector.classList.add('inactiveTab');
  //   currentTab.classList.remove('visibleTab');
  //   currentTab.classList.add('hiddenTab');
  // }
  // const tabId = event.target.dataset.tab;
  // const tabSelectorId = tabId + 'Tab';
  // const targetEl = document.getElementById(tabId);
  // targetEl.classList.remove('readyTab');
  // targetEl.classList.add('visibleTab');
  // const targetSelector = document.getElementById(tabSelectorId);
  // targetSelector.classList.remove('inactiveTab');
  // targetSelector.classList.add('activeTab');
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
  tab.classList.remove('hiddenTab');
  tab.classList.add('visibleTab');
};

const hideContentTab = (tab) => {
  console.log('hide', tab);
  tab.classList.remove('visibleTab');
  tab.classList.add('hiddenTab');
};

const showHideTab = (e) => {
  if (e.target.classList.contains('visibleTab')) return;
  e.target.classList.add('readyTab');
  e.target.classList.remove('hiddenTab');
  console.log('hiding tab ' + e.target.id);
};
