/**
 * Subscriptions JavaScript
 * Handles interactive UI enhancements.
 */
(function () {
  'use strict';

  // -----------------------------------------------------------------------
  // DOM Ready helper
  // -----------------------------------------------------------------------
  function ready(fn) {
    if (document.readyState !== 'loading') {
      fn();
    } else {
      document.addEventListener('DOMContentLoaded', fn);
    }
  }

  // -----------------------------------------------------------------------
  // Billing cycle toggle (monthly <-> annual pricing)
  // -----------------------------------------------------------------------
  function initBillingToggle() {
    var toggle = document.getElementById('xm-billing-toggle');
    if (!toggle) return;

    var monthly = document.querySelectorAll('[data-price-monthly]');
    var annual  = document.querySelectorAll('[data-price-annual]');

    function update() {
      var isAnnual = toggle.checked;
      monthly.forEach(function (el) {
        el.style.display = isAnnual ? 'none' : '';
      });
      annual.forEach(function (el) {
        el.style.display = isAnnual ? '' : 'none';
      });
      // Update hidden input in forms
      document.querySelectorAll('[name="billing_type"]').forEach(function (inp) {
        inp.value = isAnnual ? 'annual' : 'monthly';
      });
    }

    toggle.addEventListener('change', update);
    update();
  }

  // -----------------------------------------------------------------------
  // Coupon code input: trim whitespace on paste
  // -----------------------------------------------------------------------
  function initCouponInput() {
    var input = document.getElementById('coupon_code');
    if (!input) return;
    input.addEventListener('input', function () {
      this.value = this.value.toUpperCase().replace(/\s/g, '');
    });
  }

  // -----------------------------------------------------------------------
  // Cancel confirmation dialog keyboard trap
  // -----------------------------------------------------------------------
  function initCancelDialog() {
    var trigger = document.querySelector('[data-cancel-trigger]');
    var dialog  = document.getElementById('xm-cancel-form');
    var closeBtn = dialog && dialog.querySelector('[data-cancel-close]');

    if (!trigger || !dialog) return;

    trigger.addEventListener('click', function () {
      dialog.style.display = 'block';
      dialog.setAttribute('aria-hidden', 'false');
      var firstFocusable = dialog.querySelector('button, [href], input, select, textarea');
      if (firstFocusable) firstFocusable.focus();
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', function () {
        dialog.style.display = 'none';
        dialog.setAttribute('aria-hidden', 'true');
        trigger.focus();
      });
    }

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && dialog.style.display !== 'none') {
        dialog.style.display = 'none';
        dialog.setAttribute('aria-hidden', 'true');
        trigger.focus();
      }
    });
  }

  // -----------------------------------------------------------------------
  // Admin: show/hide usage-based fields based on pricing model select
  // -----------------------------------------------------------------------
  function initPricingModelToggle() {
    var modelSelect = document.getElementById('pricing_model');
    if (!modelSelect) return;

    var usageFields = document.querySelectorAll('[data-pricing-usage]');

    function update() {
      var val = modelSelect.value;
      var show = val === 'usage' || val === 'hybrid';
      usageFields.forEach(function (el) {
        el.style.display = show ? '' : 'none';
      });
    }

    modelSelect.addEventListener('change', update);
    update();
  }

  // -----------------------------------------------------------------------
  // Confirm dangerous actions (delete buttons)
  // -----------------------------------------------------------------------
  function initDeleteConfirm() {
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
      el.addEventListener('click', function (e) {
        var msg = this.getAttribute('data-confirm') || 'Are you sure?';
        if (!window.confirm(msg)) {
          e.preventDefault();
        }
      });
    });
  }

  // -----------------------------------------------------------------------
  // Auto-dismiss success notices
  // -----------------------------------------------------------------------
  function initAutoDismiss() {
    var notices = document.querySelectorAll('.xm-notice--success[data-auto-dismiss]');
    notices.forEach(function (el) {
      var delay = parseInt(el.getAttribute('data-auto-dismiss'), 10) || 5000;
      setTimeout(function () {
        el.style.transition = 'opacity 0.5s ease';
        el.style.opacity    = '0';
        setTimeout(function () { el.remove(); }, 500);
      }, delay);
    });
  }

  // -----------------------------------------------------------------------
  // Invoice print button
  // -----------------------------------------------------------------------
  function initInvoicePrint() {
    var btn = document.querySelector('.xm-invoice [data-print]');
    if (btn) {
      btn.addEventListener('click', function () {
        window.print();
      });
    }
  }

  // -----------------------------------------------------------------------
  // Table sorter  — add data-sortable to any <table> to enable;
  //                 add data-no-sort to a <th> to skip that column.
  // -----------------------------------------------------------------------
  function initTableSorter() {
    document.querySelectorAll('table[data-sortable]').forEach(function (table) {
      if (table.hasAttribute('data-sort-active')) return; // prevent double-init
      table.setAttribute('data-sort-active', '1');
      var tbody = table.querySelector('tbody');
      if (!tbody) return;

      table.querySelectorAll('thead th').forEach(function (th, colIdx) {
        if (th.hasAttribute('data-no-sort')) return;

        th.classList.add('xm-th-sort');
        th.setAttribute('role', 'columnheader');
        th.setAttribute('tabindex', '0');

        function getCellValue(row) {
          var cell = row.cells[colIdx];
          return cell ? cell.textContent.trim() : '';
        }

        function compare(a, b, asc) {
          var aVal = getCellValue(a);
          var bVal = getCellValue(b);
          // Strip currency symbols/commas to detect numeric values
          var aNum = parseFloat(aVal.replace(/[^0-9.\-]/g, ''));
          var bNum = parseFloat(bVal.replace(/[^0-9.\-]/g, ''));
          var cmp = (!isNaN(aNum) && !isNaN(bNum))
            ? aNum - bNum
            : aVal.localeCompare(bVal, undefined, { sensitivity: 'base' });
          return asc ? cmp : -cmp;
        }

        function doSort() {
          var asc = th.getAttribute('aria-sort') !== 'ascending';
          // Reset all column headers
          table.querySelectorAll('thead th').forEach(function (t) {
            t.removeAttribute('aria-sort');
          });
          th.setAttribute('aria-sort', asc ? 'ascending' : 'descending');

          var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
          rows.sort(function (a, b) { return compare(a, b, asc); });
          rows.forEach(function (row) { tbody.appendChild(row); });
        }

        th.addEventListener('click', doSort);
        th.addEventListener('keydown', function (e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            doSort();
          }
        });
      });
    });
  }

  // -----------------------------------------------------------------------
  // Init
  // -----------------------------------------------------------------------
  ready(function () {
    initBillingToggle();
    initCouponInput();
    initCancelDialog();
    initPricingModelToggle();
    initDeleteConfirm();
    initAutoDismiss();
    initInvoicePrint();
    initTableSorter();
  });

})();
