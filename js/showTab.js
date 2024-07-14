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
  if (!e.target.classList.contains('tab')) return;
  if (e.target.classList.contains('visibleTab')) return;
  e.target.classList.add('noDisplay');
};

window.onload = () => {
  // if it's a blog link, open the blog tab
  const urlParams = new URLSearchParams(window.location.search);
  const searchTab = urlParams.get('blog_id');
  if (!searchTab) return;
  const activeTab = document.querySelector('.activeTab');
  const visibleTab = document.querySelector('.visibleTab');
  activeTab.classList.remove('activeTab');
  activeTab.classList.add('inactiveTab');
  visibleTab.classList.remove('visibleTab');
  visibleTab.classList.add('hiddenTab');
  const blogTab = document.getElementById('blogTab');
  const blog = document.getElementById('blog');
  blogTab.classList.remove('inactiveTab');
  blogTab.classList.add('activeTab');
  blog.classList.remove('hiddenTab');
  blog.classList.add('visibleTab');
};
