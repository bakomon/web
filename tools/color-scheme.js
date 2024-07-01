(function() {
  'use strict'

  const browser_theme = window.matchMedia('(prefers-color-scheme:dark)').matches ? 'dark' : 'light';
  let stored_theme = localStorage.getItem('bmv_theme');
  if (! /^(light|dark)$/.test(String(stored_theme))) stored_theme = null;
  let user_theme = stored_theme ? stored_theme : browser_theme;
  
  document.documentElement.classList.remove('system', 'light', 'dark');
  if (!stored_theme) document.documentElement.classList.add('system');
  document.documentElement.classList.add(user_theme);
  localStorage.setItem('bmv_theme', (stored_theme ? user_theme : 'system'));
})();
