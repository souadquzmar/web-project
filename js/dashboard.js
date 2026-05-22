

document.addEventListener('DOMContentLoaded', () => {

  const pwInput = document.getElementById('new-password');
  const fill = document.getElementById('strength-fill');
  const label = document.getElementById('strength-label');
  if (pwInput && fill) {
    pwInput.addEventListener('input', () => {
      const v = pwInput.value;
      let score = 0;
      if (v.length >= 8) score++;
      if (/[A-Z]/.test(v)) score++;
      if (/[0-9]/.test(v)) score++;
      if (/[^A-Za-z0-9]/.test(v)) score++;
      const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
      const labels = ['Weak', 'Fair', 'Good', 'Strong'];
      fill.style.width = (score * 25) + '%';
      fill.style.background = colors[score - 1] || '#e5e7eb';
      if (label) label.textContent = score ? labels[score - 1] : '';
    });
  }

  const pwNew = document.getElementById('new-password');
  const pwConfirm = document.getElementById('confirm-password');
  const pwSuccess = document.getElementById('pwSuccess');
  const pwBtn = document.getElementById('updatePasswordBtn');
  if (pwBtn) {
    pwBtn.addEventListener('click', () => {
      if (pwNew && pwNew.value.length < 8) {
        pwNew.classList.add('error');
        pwNew.focus();
        return;
      }
      if (pwNew && pwConfirm && pwNew.value !== pwConfirm.value) {
        pwConfirm.style.borderColor = '#ef4444';
        pwConfirm.focus();
        return;
      }
      pwBtn.innerHTML = '<i class="fa-solid fa-check"></i> Password Updated!';
      pwBtn.style.background = '#22c55e';
      pwBtn.style.boxShadow = '0 4px 14px rgba(34,197,94,0.4)';
      if (pwSuccess) pwSuccess.classList.add('show');
      setTimeout(() => {
        pwBtn.innerHTML = '<i class="fa-solid fa-lock"></i> Update Password';
        pwBtn.style.background = '';
        pwBtn.style.boxShadow = '';
        if (pwSuccess) pwSuccess.classList.remove('show');
      }, 3000);
    });
  }

  if (pwConfirm && pwNew) {
    pwConfirm.addEventListener('input', () => {
      pwConfirm.style.borderColor = pwConfirm.value === pwNew.value ? '#22c55e' : '#ef4444';
    });
  }

  document.querySelectorAll('.payment-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
    });
  });

  const addCardBtn = document.getElementById('addCardBtn');
  if (addCardBtn) {
    addCardBtn.addEventListener('click', () => {
      addCardBtn.innerHTML = '<i class="fa-solid fa-check"></i> Card Added!';
      addCardBtn.style.background = '#22c55e';
      addCardBtn.style.boxShadow = '0 4px 14px rgba(34,197,94,0.4)';
      setTimeout(() => {
        addCardBtn.innerHTML = '<i class="fa-solid fa-plus"></i> Add Card';
        addCardBtn.style.background = '';
        addCardBtn.style.boxShadow = '';
      }, 2500);
    });
  }

  document.querySelectorAll('.delete-row').forEach(btn => {
    btn.addEventListener('click', () => {
      const row = btn.closest('tr');
      if (row && confirm('Delete this listing?')) {
        row.style.opacity = '0';
        row.style.transition = 'opacity 0.3s';
        setTimeout(() => row.remove(), 300);
      }
    });
  });
  const saveProfileBtn = document.getElementById('saveProfileBtn');
  if (saveProfileBtn) {
    saveProfileBtn.addEventListener('click', () => {
      saveProfileBtn.innerHTML = '<i class="fa-solid fa-check"></i> Saved!';
      saveProfileBtn.style.background = '#22c55e';
      saveProfileBtn.style.boxShadow = '0 4px 14px rgba(34,197,94,0.4)';
      setTimeout(() => {
        saveProfileBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Changes';
        saveProfileBtn.style.background = '';
        saveProfileBtn.style.boxShadow = '';
      }, 2500);
    });
  }

  const fileInput = document.getElementById('avatarFileInput');
  const imgEl = document.getElementById('profileAvatarImg');
  const overlay = document.getElementById('avatarOverlay');
  const changeBtn = document.getElementById('changePhotoBtn');

  function triggerAvatarUpload() { if (fileInput) fileInput.click(); }
  if (changeBtn) changeBtn.addEventListener('click', triggerAvatarUpload);
  if (overlay) {
    overlay.addEventListener('click', triggerAvatarUpload);
    overlay.parentElement.addEventListener('mouseenter', () => { overlay.style.opacity = '1'; });
    overlay.parentElement.addEventListener('mouseleave', () => { overlay.style.opacity = '0'; });
  }
  if (fileInput) {
    fileInput.addEventListener('change', e => {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = ev => {
        if (imgEl) imgEl.src = ev.target.result;
        const sidebarAvatar = document.querySelector('.dash-avatar');
        if (sidebarAvatar) sidebarAvatar.src = ev.target.result;
      };
      reader.readAsDataURL(file);
    });
  }

  const savePropertyBtn = document.getElementById('savePropertyBtn');
  const saveSuccess = document.getElementById('saveSuccess');
  if (savePropertyBtn) {
    savePropertyBtn.addEventListener('click', () => {
      savePropertyBtn.innerHTML = '<i class="fa-solid fa-check"></i> Saved!';
      savePropertyBtn.style.background = '#22c55e';
      savePropertyBtn.style.boxShadow = '0 4px 14px rgba(34,197,94,0.4)';
      if (saveSuccess) saveSuccess.style.display = 'flex';
      setTimeout(() => {
        savePropertyBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Property';
        savePropertyBtn.style.background = '';
        savePropertyBtn.style.boxShadow = '';
        if (saveSuccess) saveSuccess.style.display = 'none';
      }, 3000);
    });
  }

  if (typeof Chart !== 'undefined') {
    const D = window.DASH_CHART_DATA || {};
    const ctx1 = document.getElementById('chartViews');
    if (ctx1 && D.months) {
      new Chart(ctx1, {
        type: 'line',
        data: {
          labels: D.months,
          datasets: [
            {
              label: D.viewsLabel || 'Views',
              data: D.views || [],
              borderColor: '#ff385c',
              backgroundColor: 'rgba(255,56,92,0.08)',
              borderWidth: 2.5, tension: 0.4, fill: true,
              pointRadius: 3, pointBackgroundColor: '#ff385c',
            },
            {
              label: D.inquiriesLabel || 'Inquiries',
              data: D.inquiries || [],
              borderColor: '#3b82f6',
              backgroundColor: 'rgba(59,130,246,0.06)',
              borderWidth: 2, tension: 0.4, fill: true,
              pointRadius: 3, pointBackgroundColor: '#3b82f6',
            },
          ],
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'top', labels: { font: { family: 'Montserrat', size: 12, weight: '600' }, boxWidth: 10, padding: 18 } },
            tooltip: { mode: 'index', intersect: false },
          },
          scales: {
            x: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { family: 'Montserrat', size: 11 } } },
            y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { family: 'Montserrat', size: 11 } } },
          },
        },
      });
    }

    const ctx2 = document.getElementById('chartStatus');
    if (ctx2 && D.donutData) {
      new Chart(ctx2, {
        type: 'doughnut',
        data: {
          labels: D.donutLabels || ['Active', 'Pending', 'Inactive'],
          datasets: [{ data: D.donutData, backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'], borderWidth: 0, hoverOffset: 8 }],
        },
        options: {
          responsive: true, cutout: '68%',
          plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: c => ` ${c.label}: ${c.parsed}` } },
          },
        },
      });
    }
  }

  const downloadPdfBtn = document.getElementById('downloadPdfBtn');
  if (downloadPdfBtn) {
    downloadPdfBtn.addEventListener('click', () => {
      const invoiceEl = document.querySelector('.dash-card');
      if (!invoiceEl) return;

      const orig = downloadPdfBtn.innerHTML;
      downloadPdfBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating…';
      downloadPdfBtn.disabled = true;

      const actionsRow = document.querySelector('.invoice-actions');
      if (actionsRow) actionsRow.style.display = 'none';

      html2canvas(invoiceEl, { scale: 2, useCORS: true, backgroundColor: '#ffffff', logging: false })
        .then(canvas => {
          if (actionsRow) actionsRow.style.display = '';
          const { jsPDF } = window.jspdf;
          const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
          const margin = 12;
          const pageW = pdf.internal.pageSize.getWidth();
          const pageH = pdf.internal.pageSize.getHeight();
          const imgW = pageW - margin * 2;
          const imgH = Math.min((canvas.height * imgW) / canvas.width, pageH - margin * 2);
          pdf.addImage(canvas.toDataURL('image/png'), 'PNG', margin, margin, imgW, imgH);
          pdf.save('FindHouses-Invoice-FH-2024-0042.pdf');

          downloadPdfBtn.innerHTML = '<i class="fa-solid fa-check"></i> Downloaded!';
          downloadPdfBtn.style.borderColor = '#22c55e';
          downloadPdfBtn.style.color = '#22c55e';
          setTimeout(() => {
            downloadPdfBtn.innerHTML = orig;
            downloadPdfBtn.disabled = false;
            downloadPdfBtn.style.borderColor = '';
            downloadPdfBtn.style.color = '';
          }, 2500);
        })
        .catch(() => {
          if (actionsRow) actionsRow.style.display = '';
          downloadPdfBtn.innerHTML = orig;
          downloadPdfBtn.disabled = false;
        });
    });
  }

});
