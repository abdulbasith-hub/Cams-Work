/**
 * DGA responsive helpers:
 * - Mobile hamburger / off-canvas department menu
 * - Structure-map details open on click/tap (not hover)
 */
(function () {
  'use strict';

  var desktopQuery = window.matchMedia('(min-width: 992px)');

  function getParts() {
    return {
      toggle: document.querySelector('[data-dga-menu-toggle]'),
      drawer: document.getElementById('dgaMobileMenu'),
      closes: document.querySelectorAll('[data-dga-menu-close]'),
      backdrop: document.querySelector('.dga-mobile-backdrop')
    };
  }

  function setOpen(isOpen) {
    var parts = getParts();
    if (!parts.drawer || !parts.toggle) {
      return;
    }

    // Desktop: drawer is in normal document flow (department strip visible)
    if (desktopQuery.matches) {
      document.body.classList.remove('dga-menu-open');
      parts.toggle.setAttribute('aria-expanded', 'false');
      parts.toggle.setAttribute('aria-label', 'Open navigation menu');
      parts.drawer.removeAttribute('aria-hidden');
      if (parts.backdrop) {
        parts.backdrop.setAttribute('aria-hidden', 'true');
      }
      var desktopIcon = parts.toggle.querySelector('i');
      if (desktopIcon) {
        desktopIcon.className = 'fas fa-bars';
      }
      return;
    }

    document.body.classList.toggle('dga-menu-open', isOpen);
    parts.toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    parts.toggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
    parts.drawer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

    if (parts.backdrop) {
      parts.backdrop.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
    }

    var icon = parts.toggle.querySelector('i');
    if (icon) {
      icon.className = isOpen ? 'fas fa-times' : 'fas fa-bars';
    }
  }

  function closeMenu() {
    setOpen(false);
  }

  function toggleMenu() {
    setOpen(!document.body.classList.contains('dga-menu-open'));
  }

	  function initDepartmentMenus() {
	    document.querySelectorAll('.dga-department-item').forEach(function (item) {
	      var title = item.querySelector('.dga-department-title');
	      if (!title) {
	        return;
	      }

	      item.addEventListener('pointerdown', function () {
	        item.setAttribute('data-dga-was-open', item.classList.contains('is-open') ? 'true' : 'false');
	      }, true);

	      item.addEventListener('click', function (event) {
	        if (event.target.closest('.dga-department-menu a')) {
	          return;
	        }

	        event.preventDefault();
	        event.stopPropagation();

	        var priorOpenState = item.getAttribute('data-dga-was-open');
	        var wasOpen = priorOpenState === 'true'
	          ? true
	          : priorOpenState === 'false'
	            ? false
	            : item.classList.contains('is-open');
	        item.removeAttribute('data-dga-was-open');

	        var willOpen = !wasOpen;
	        document.querySelectorAll('.dga-department-item.is-open').forEach(function (openItem) {
	          openItem.classList.remove('is-open');
          var openTitle = openItem.querySelector('.dga-department-title');
          if (openTitle) {
            openTitle.setAttribute('aria-expanded', 'false');
          }
        });

	        item.classList.toggle('is-open', willOpen);
	        title.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
	      });
	    });
	  }

  function ensureOrgPopupBackdrop() {
    var el = document.querySelector('.dga-org-popup-backdrop');
    if (el) {
      return el;
    }
    el = document.createElement('div');
    el.className = 'dga-org-popup-backdrop';
    el.setAttribute('aria-hidden', 'true');
    document.body.appendChild(el);
    el.addEventListener('click', closeAllOrgPopups);
    return el;
  }

  function closeAllOrgPopups() {
    document.querySelectorAll('.org-node.is-popup-open').forEach(function (node) {
      node.classList.remove('is-popup-open');
      node.setAttribute('aria-expanded', 'false');
    });
    document.body.classList.remove('dga-org-popup-open');
  }

  function initMobileMenu() {
    var parts = getParts();
    if (!parts.toggle || !parts.drawer) {
      return;
    }

    setOpen(false);

    parts.toggle.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      toggleMenu();
    });

    parts.closes.forEach(function (el) {
      el.addEventListener('click', function (event) {
        event.preventDefault();
        closeMenu();
      });
    });

    parts.drawer.addEventListener('click', function (event) {
      var link = event.target.closest('.dga-department-menu a, .dga-mobile-nav a');
      if (link && !desktopQuery.matches) {
        window.setTimeout(closeMenu, 50);
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        if (document.body.classList.contains('dga-menu-open')) {
          closeMenu();
          parts.toggle.focus();
        }
        closeAllOrgPopups();
      }
    });

    function onViewportChange() {
      if (desktopQuery.matches) {
        closeMenu();
      }
      closeAllOrgPopups();
    }

    if (typeof desktopQuery.addEventListener === 'function') {
      desktopQuery.addEventListener('change', onViewportChange);
    } else if (typeof desktopQuery.addListener === 'function') {
      desktopQuery.addListener(onViewportChange);
    }

    window.addEventListener('resize', onViewportChange);
  }

  function initOrgPopups() {
    var nodes = document.querySelectorAll('.org-node');
    if (!nodes.length) {
      return;
    }

    ensureOrgPopupBackdrop();

    nodes.forEach(function (node) {
      if (!node.querySelector('.org-popup')) {
        return;
      }

      node.setAttribute('role', 'button');
      node.setAttribute('aria-expanded', 'false');
      node.setAttribute('tabindex', node.getAttribute('tabindex') || '0');

      node.addEventListener('click', function (event) {
        // Allow selecting / interacting with table content inside an open popup
        if (event.target.closest('.org-popup') && node.classList.contains('is-popup-open')) {
          return;
        }

        event.preventDefault();
        event.stopPropagation();

        var willOpen = !node.classList.contains('is-popup-open');
        closeAllOrgPopups();

        if (willOpen) {
          node.classList.add('is-popup-open');
          node.setAttribute('aria-expanded', 'true');
          document.body.classList.add('dga-org-popup-open');
        }
      });

      node.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          node.click();
        }
      });
    });

    document.addEventListener('click', function (event) {
      if (!event.target.closest('.org-node.is-popup-open')) {
        closeAllOrgPopups();
      }
    });
  }

  function initHeroMenuGroups() {
    var groups = document.querySelectorAll('.dga-hero-menu-group.has-children');
    groups.forEach(function (group) {
      var toggle = group.querySelector('[data-dga-menu-toggle]');
      if (!toggle) {
        return;
      }
      toggle.setAttribute('aria-expanded', 'false');
      toggle.addEventListener('click', function (event) {
        event.preventDefault();
        var expanded = group.classList.toggle('is-expanded');
        toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initMobileMenu();
    initDepartmentMenus();
    initHeroMenuGroups();
    // initOrgPopups() disabled for now: org-chart boxes should not be
    // clickable yet (deferred, to be revisited later). Desktop hover/focus
    // still reveals each box's popup via CSS; this only removes the
    // click-to-toggle behavior needed for touch devices.
  });
})();
