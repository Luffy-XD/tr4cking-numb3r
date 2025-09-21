'use strict';

const STORAGE_KEYS = {
  users: 'arsip_users',
  categories: 'arsip_categories',
  mails: 'arsip_mails',
  settings: 'arsip_settings'
};

const DEFAULT_USERS = [
  {
    id: 'user-admin',
    name: 'Administrator',
    email: 'admin@bpsdm.aceh.go.id',
    username: 'admin',
    password: 'admin123',
    role: 'admin',
    status: 'Active'
  },
  {
    id: 'user-staff',
    name: 'Staf Arsip',
    email: 'staff@bpsdm.aceh.go.id',
    username: 'staff',
    password: 'staff123',
    role: 'staff',
    status: 'Active'
  }
];

const DEFAULT_CATEGORIES = [
  {
    id: 'cat-umum',
    name: 'Umum',
    description: 'Surat resmi terkait kegiatan umum instansi'
  },
  {
    id: 'cat-kepegawaian',
    name: 'Kepegawaian',
    description: 'Surat terkait pengembangan sumber daya manusia'
  },
  {
    id: 'cat-keuangan',
    name: 'Keuangan',
    description: 'Surat mengenai perencanaan dan pelaporan anggaran'
  }
];

const DEFAULT_MAILS = [
  {
    id: 'mail-in-1',
    type: 'incoming',
    mailNo: '001/SM/I/2024',
    sender: 'Kementerian PANRB',
    recipient: '',
    date: '2024-01-10',
    subject: 'Undangan Rapat Koordinasi Nasional',
    categoryId: 'cat-umum',
    createdBy: 'user-admin',
    createdAt: '2024-01-10T08:00:00Z',
    fileName: null,
    fileData: null
  },
  {
    id: 'mail-in-2',
    type: 'incoming',
    mailNo: '012/SM/II/2024',
    sender: 'BKN Regional XIII',
    recipient: '',
    date: '2024-02-04',
    subject: 'Penyampaian Materi Diklat Fungsional',
    categoryId: 'cat-kepegawaian',
    createdBy: 'user-staff',
    createdAt: '2024-02-04T07:30:00Z',
    fileName: null,
    fileData: null
  },
  {
    id: 'mail-out-1',
    type: 'outgoing',
    mailNo: '034/SK/III/2024',
    sender: '',
    recipient: 'Sekretariat Daerah Aceh',
    date: '2024-03-18',
    subject: 'Laporan Hasil Pelatihan Kepemimpinan',
    categoryId: 'cat-kepegawaian',
    createdBy: 'user-admin',
    createdAt: '2024-03-18T10:15:00Z',
    fileName: null,
    fileData: null
  },
  {
    id: 'mail-out-2',
    type: 'outgoing',
    mailNo: '041/SK/IV/2024',
    sender: '',
    recipient: 'BPSDM Sumatera Utara',
    date: '2024-04-02',
    subject: 'Permohonan Kerja Sama Program Magang',
    categoryId: 'cat-umum',
    createdBy: 'user-staff',
    createdAt: '2024-04-02T09:00:00Z',
    fileName: null,
    fileData: null
  }
];

const DEFAULT_SETTINGS = {
  institutionName: 'BPSDM Aceh',
  address: 'Jl. T. Nyak Arief No.219, Banda Aceh',
  logo: null,
  maxFileSize: 10,
  allowedTypes: ['application/pdf']
};

const NAV_ITEMS = {
  admin: [
    { view: 'admin-dashboard', icon: 'fa-solid fa-chart-pie', label: 'Dashboard' },
    { view: 'incoming-mail', icon: 'fa-solid fa-inbox', label: 'Surat Masuk' },
    { view: 'outgoing-mail', icon: 'fa-solid fa-paper-plane', label: 'Surat Keluar' },
    { view: 'mail-categories', icon: 'fa-solid fa-folder-tree', label: 'Kategori Surat' },
    { view: 'reports', icon: 'fa-solid fa-chart-line', label: 'Laporan' },
    { view: 'user-management', icon: 'fa-solid fa-users-gear', label: 'Manajemen Pengguna' },
    { view: 'system-settings', icon: 'fa-solid fa-sliders', label: 'Pengaturan Sistem' },
    { action: 'logout', icon: 'fa-solid fa-right-from-bracket', label: 'Keluar' }
  ],
  staff: [
    { view: 'staff-dashboard', icon: 'fa-solid fa-chart-pie', label: 'Dashboard' },
    { view: 'incoming-mail', icon: 'fa-solid fa-inbox', label: 'Surat Masuk' },
    { view: 'outgoing-mail', icon: 'fa-solid fa-paper-plane', label: 'Surat Keluar' },
    { view: 'reports', icon: 'fa-solid fa-chart-line', label: 'Laporan' },
    { action: 'logout', icon: 'fa-solid fa-right-from-bracket', label: 'Keluar' }
  ]
};

const MONTH_LABELS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
const DEFAULT_LOGO_PATH = 'assets/img/logo.svg';

let state = {
  users: [],
  categories: [],
  mails: [],
  settings: {}
};

let currentUser = null;
let mailChartInstance = null;
let reportResults = [];
let activeView = '';

document.addEventListener('DOMContentLoaded', () => {
  initializeState();
  setupLogin();
  setupEventHandlers();
  restoreSession();
  const reportPeriodSelect = document.getElementById('report-period');
  if (reportPeriodSelect) {
    renderReportPeriodInput(reportPeriodSelect.value);
  }
});

function initializeState() {
  state.users = loadOrDefault(STORAGE_KEYS.users, DEFAULT_USERS);
  state.categories = loadOrDefault(STORAGE_KEYS.categories, DEFAULT_CATEGORIES);
  state.mails = loadOrDefault(STORAGE_KEYS.mails, DEFAULT_MAILS);
  state.settings = loadOrDefault(STORAGE_KEYS.settings, DEFAULT_SETTINGS);
}

function loadOrDefault(key, defaultValue) {
  const stored = localStorage.getItem(key);
  if (stored) {
    try {
      return JSON.parse(stored);
    } catch (error) {
      console.error(`Gagal mengurai data ${key}:`, error);
    }
  }
  const clone = typeof structuredClone === 'function'
    ? structuredClone(defaultValue)
    : JSON.parse(JSON.stringify(defaultValue));
  localStorage.setItem(key, JSON.stringify(clone));
  return clone;
}

function setupLogin() {
  const loginForm = document.getElementById('login-form');
  if (!loginForm) return;

  loginForm.addEventListener('submit', (event) => {
    event.preventDefault();
    hideLoginMessage();
    const identity = document.getElementById('login-identity').value.trim().toLowerCase();
    const password = document.getElementById('login-password').value.trim();

    const user = state.users.find((item) => {
      return (
        (item.email.toLowerCase() === identity || item.username.toLowerCase() === identity) &&
        item.password === password
      );
    });

    if (!user) {
      showLoginMessage('Email/username atau password tidak sesuai.', 'error');
      return;
    }

    if (user.status !== 'Active') {
      showLoginMessage('Akun Anda sedang dinonaktifkan. Hubungi administrator.', 'error');
      return;
    }

    currentUser = user;
    sessionStorage.setItem('arsip_current_user', currentUser.id);
    enterApplication();
    hideLoginMessage();
    displayToast(`Selamat datang, ${user.name}!`, 'success');
    document.getElementById('login-form').reset();
  });
}

function setupEventHandlers() {
  const incomingForm = document.getElementById('incoming-form');
  if (incomingForm) {
    incomingForm.addEventListener('submit', handleIncomingSubmit);
  }

  const incomingFile = document.getElementById('incoming-file');
  if (incomingFile) {
    incomingFile.addEventListener('change', () => handlePdfPreview(incomingFile, 'incoming-preview-wrapper', 'incoming-preview'));
  }

  const outgoingForm = document.getElementById('outgoing-form');
  if (outgoingForm) {
    outgoingForm.addEventListener('submit', handleOutgoingSubmit);
  }

  const outgoingFile = document.getElementById('outgoing-file');
  if (outgoingFile) {
    outgoingFile.addEventListener('change', () => handlePdfPreview(outgoingFile, 'outgoing-preview-wrapper', 'outgoing-preview'));
  }

  const incomingTable = document.getElementById('incoming-table-body');
  if (incomingTable) {
    incomingTable.addEventListener('click', handleMailAction);
  }

  const outgoingTable = document.getElementById('outgoing-table-body');
  if (outgoingTable) {
    outgoingTable.addEventListener('click', handleMailAction);
  }

  const categoryForm = document.getElementById('category-form');
  if (categoryForm) {
    categoryForm.addEventListener('submit', handleCategorySubmit);
  }

  const categoryReset = document.getElementById('category-reset');
  if (categoryReset) {
    categoryReset.addEventListener('click', () => {
      document.getElementById('category-id').value = '';
    });
  }

  const categoryTable = document.getElementById('category-table-body');
  if (categoryTable) {
    categoryTable.addEventListener('click', handleCategoryAction);
  }

  const reportPeriodSelect = document.getElementById('report-period');
  if (reportPeriodSelect) {
    reportPeriodSelect.addEventListener('change', (event) => {
      renderReportPeriodInput(event.target.value);
    });
  }

  const reportForm = document.getElementById('report-filter');
  if (reportForm) {
    reportForm.addEventListener('submit', handleReportSubmit);
  }

  const pdfButton = document.getElementById('export-pdf');
  if (pdfButton) {
    pdfButton.addEventListener('click', exportReportToPdf);
  }

  const excelButton = document.getElementById('export-excel');
  if (excelButton) {
    excelButton.addEventListener('click', exportReportToExcel);
  }

  const userForm = document.getElementById('user-form');
  if (userForm) {
    userForm.addEventListener('submit', handleUserSubmit);
  }

  const userTable = document.getElementById('user-table-body');
  if (userTable) {
    userTable.addEventListener('click', handleUserAction);
  }

  const institutionForm = document.getElementById('institution-form');
  if (institutionForm) {
    institutionForm.addEventListener('submit', handleInstitutionSubmit);
  }

  const fileSettingsForm = document.getElementById('file-settings-form');
  if (fileSettingsForm) {
    fileSettingsForm.addEventListener('submit', handleFileSettingsSubmit);
  }

  const backupButton = document.getElementById('backup-btn');
  if (backupButton) {
    backupButton.addEventListener('click', downloadBackup);
  }

  const restoreInput = document.getElementById('restore-input');
  if (restoreInput) {
    restoreInput.addEventListener('change', handleRestoreUpload);
  }
}

function restoreSession() {
  const userId = sessionStorage.getItem('arsip_current_user');
  if (!userId) return;
  const user = state.users.find((item) => item.id === userId && item.status === 'Active');
  if (!user) return;
  currentUser = user;
  enterApplication();
}

function enterApplication() {
  const loginView = document.getElementById('login-view');
  const appView = document.getElementById('app');
  if (loginView) loginView.classList.add('hidden');
  if (appView) appView.classList.remove('hidden');

  renderNavigation();
  renderUserInfo();
  applyInstitutionSettings();
  populateCategoryOptions();
  renderMailTables();
  renderCategories();
  renderUsers();
  renderDashboards();
  renderSettingsForms();

  const defaultView = currentUser.role === 'admin' ? 'admin-dashboard' : 'staff-dashboard';
  setActiveView(defaultView);
}

function renderNavigation() {
  const navContainer = document.getElementById('nav-links');
  if (!navContainer) return;

  const navItems = NAV_ITEMS[currentUser.role] || [];
  navContainer.innerHTML = '';

  navItems.forEach((item) => {
    const li = document.createElement('li');
    const button = document.createElement('button');
    button.className = 'nav-link';
    button.innerHTML = `<i class="${item.icon}"></i><span>${item.label}</span>`;
    if (item.action === 'logout') {
      button.addEventListener('click', handleLogout);
    } else {
      button.dataset.view = item.view;
      button.addEventListener('click', () => setActiveView(item.view, item.label));
    }
    li.appendChild(button);
    navContainer.appendChild(li);
  });
}

function setActiveView(viewId, label) {
  if (!viewId) return;
  activeView = viewId;
  document.querySelectorAll('.page-section').forEach((section) => {
    section.classList.add('hidden');
    section.classList.remove('active');
  });

  const target = document.getElementById(viewId);
  if (target) {
    target.classList.remove('hidden');
    target.classList.add('active');
  }

  document.querySelectorAll('.nav-link').forEach((link) => {
    link.classList.remove('active');
    if (link.dataset.view === viewId) {
      link.classList.add('active');
    }
  });

  if (label) {
    const pageTitle = document.getElementById('page-title');
    if (pageTitle) pageTitle.textContent = label;
  } else {
    const navItems = NAV_ITEMS[currentUser.role] || [];
    const matched = navItems.find((item) => item.view === viewId);
    if (matched) {
      const pageTitle = document.getElementById('page-title');
      if (pageTitle) pageTitle.textContent = matched.label;
    }
  }

  if (viewId === 'admin-dashboard' || viewId === 'staff-dashboard') {
    renderDashboards();
  }
  if (viewId === 'reports' && reportResults.length === 0) {
    renderReportTable([]);
  }
}

function renderUserInfo() {
  const nameEl = document.getElementById('user-name');
  const roleEl = document.getElementById('user-role');
  if (nameEl) nameEl.textContent = currentUser?.name || '';
  if (roleEl) roleEl.textContent = currentUser?.role === 'admin' ? 'Administrator' : 'Staf';
}

function applyInstitutionSettings() {
  const { institutionName, address, logo } = state.settings;
  const titleEl = document.getElementById('institution-title');
  const addressEl = document.getElementById('institution-address');
  const sidebarLogo = document.getElementById('sidebar-logo');
  const logoPreview = document.getElementById('logo-preview');

  if (titleEl) titleEl.textContent = institutionName || 'BPSDM Aceh';
  if (addressEl) addressEl.textContent = address || '';
  if (sidebarLogo) sidebarLogo.src = logo || DEFAULT_LOGO_PATH;
  if (logoPreview) logoPreview.src = logo || DEFAULT_LOGO_PATH;
  document.title = `Sistem Arsip Surat ${institutionName || 'BPSDM Aceh'}`;

  const nameInput = document.getElementById('institution-name');
  const addressInput = document.getElementById('institution-address-input');
  if (nameInput) nameInput.value = institutionName || '';
  if (addressInput) addressInput.value = address || '';
}

function renderMailTables() {
  const incomingBody = document.getElementById('incoming-table-body');
  const outgoingBody = document.getElementById('outgoing-table-body');

  if (incomingBody) {
    const incoming = state.mails.filter((mail) => mail.type === 'incoming');
    incomingBody.innerHTML = incoming.length
      ? incoming
          .map((mail) => mailRowTemplate(mail))
          .join('')
      : emptyRowTemplate(7, 'Belum ada surat masuk.');
  }

  if (outgoingBody) {
    const outgoing = state.mails.filter((mail) => mail.type === 'outgoing');
    outgoingBody.innerHTML = outgoing.length
      ? outgoing
          .map((mail) => mailRowTemplate(mail))
          .join('')
      : emptyRowTemplate(7, 'Belum ada surat keluar.');
  }
}

function mailRowTemplate(mail) {
  const categoryName = getCategoryName(mail.categoryId);
  const creatorName = getUserName(mail.createdBy);
  const person = mail.type === 'incoming' ? mail.sender || '-' : mail.recipient || '-';
  const date = formatDate(mail.date);
  return `
    <tr>
      <td>${mail.mailNo}</td>
      <td>${person}</td>
      <td>${date}</td>
      <td>${mail.subject}</td>
      <td>${categoryName}</td>
      <td>${creatorName}</td>
      <td>
        <div class="actions-inline">
          <button class="btn btn-text" data-action="view" data-id="${mail.id}" data-type="${mail.type}">
            <i class="fa-solid fa-eye"></i> Lihat
          </button>
          <button class="btn btn-text" data-action="delete" data-id="${mail.id}" data-type="${mail.type}">
            <i class="fa-solid fa-trash"></i> Hapus
          </button>
        </div>
      </td>
    </tr>
  `;
}

function emptyRowTemplate(colspan, message) {
  return `<tr><td colspan="${colspan}" class="empty-row">${message}</td></tr>`;
}

async function handleIncomingSubmit(event) {
  event.preventDefault();
  if (!currentUser) return;

  const mailNo = document.getElementById('incoming-number').value.trim();
  const sender = document.getElementById('incoming-sender').value.trim();
  const date = document.getElementById('incoming-date').value;
  const subject = document.getElementById('incoming-subject').value.trim();
  const categoryId = document.getElementById('incoming-category').value;
  const fileInput = document.getElementById('incoming-file');
  const file = fileInput.files[0];

  if (!file || !validatePdf(file)) {
    fileInput.value = '';
    hidePreview('incoming-preview-wrapper', 'incoming-preview');
    return;
  }

  const fileData = await readFileAsDataUrl(file);

  const newMail = {
    id: generateId('mail'),
    type: 'incoming',
    mailNo,
    sender,
    recipient: '',
    date,
    subject,
    categoryId,
    createdBy: currentUser.id,
    createdAt: new Date().toISOString(),
    fileName: file.name,
    fileData
  };

  state.mails.push(newMail);
  persistMails();
  renderMailTables();
  renderDashboards();
  renderCategories();
  if (activeView === 'reports') {
    handleReportSubmit(new Event('submit'));
  }

  event.target.reset();
  hidePreview('incoming-preview-wrapper', 'incoming-preview');
  displayToast('Surat masuk berhasil disimpan.', 'success');
}

async function handleOutgoingSubmit(event) {
  event.preventDefault();
  if (!currentUser) return;

  const mailNo = document.getElementById('outgoing-number').value.trim();
  const recipient = document.getElementById('outgoing-recipient').value.trim();
  const date = document.getElementById('outgoing-date').value;
  const subject = document.getElementById('outgoing-subject').value.trim();
  const categoryId = document.getElementById('outgoing-category').value;
  const fileInput = document.getElementById('outgoing-file');
  const file = fileInput.files[0];

  if (!file || !validatePdf(file)) {
    fileInput.value = '';
    hidePreview('outgoing-preview-wrapper', 'outgoing-preview');
    return;
  }

  const fileData = await readFileAsDataUrl(file);

  const newMail = {
    id: generateId('mail'),
    type: 'outgoing',
    mailNo,
    sender: '',
    recipient,
    date,
    subject,
    categoryId,
    createdBy: currentUser.id,
    createdAt: new Date().toISOString(),
    fileName: file.name,
    fileData
  };

  state.mails.push(newMail);
  persistMails();
  renderMailTables();
  renderDashboards();
  renderCategories();
  if (activeView === 'reports') {
    handleReportSubmit(new Event('submit'));
  }

  event.target.reset();
  hidePreview('outgoing-preview-wrapper', 'outgoing-preview');
  displayToast('Surat keluar berhasil disimpan.', 'success');
}

function handlePdfPreview(input, wrapperId, embedId) {
  const file = input.files[0];
  if (!file) {
    hidePreview(wrapperId, embedId);
    return;
  }

  if (!validatePdf(file)) {
    input.value = '';
    hidePreview(wrapperId, embedId);
    return;
  }

  const wrapper = document.getElementById(wrapperId);
  const embed = document.getElementById(embedId);
  if (!wrapper || !embed) return;

  if (embed.dataset.url) {
    URL.revokeObjectURL(embed.dataset.url);
  }

  const url = URL.createObjectURL(file);
  embed.src = url;
  embed.dataset.url = url;
  wrapper.classList.remove('hidden');
}

function hidePreview(wrapperId, embedId) {
  const wrapper = document.getElementById(wrapperId);
  const embed = document.getElementById(embedId);
  if (embed && embed.dataset.url) {
    URL.revokeObjectURL(embed.dataset.url);
    delete embed.dataset.url;
  }
  if (embed) {
    embed.removeAttribute('src');
  }
  if (wrapper) {
    wrapper.classList.add('hidden');
  }
}

function validatePdf(file) {
  if (!file) return false;
  const allowedTypes = state.settings.allowedTypes || ['application/pdf'];
  const maxSize = Number(state.settings.maxFileSize || 10) * 1024 * 1024;

  if (allowedTypes.length && !allowedTypes.includes(file.type || '')) {
    displayToast('Berkas harus berformat PDF.', 'error');
    return false;
  }

  if (file.size > maxSize) {
    displayToast(`Ukuran berkas melebihi ${state.settings.maxFileSize} MB.`, 'error');
    return false;
  }

  return true;
}

function handleMailAction(event) {
  const button = event.target.closest('button[data-action]');
  if (!button) return;
  const id = button.dataset.id;
  const action = button.dataset.action;

  if (action === 'view') {
    viewMail(id);
  } else if (action === 'delete') {
    deleteMail(id);
  }
}

function viewMail(id) {
  const mail = state.mails.find((item) => item.id === id);
  if (!mail) {
    displayToast('Berkas surat tidak ditemukan.', 'error');
    return;
  }

  if (mail.fileData) {
    const newWindow = window.open();
    if (newWindow) {
      newWindow.document.write(`<iframe src="${mail.fileData}" style="width:100%;height:100%" frameborder="0"></iframe>`);
    } else {
      displayToast('Aktifkan pop-up untuk melihat berkas.', 'error');
    }
  } else {
    displayToast('Berkas PDF belum tersedia untuk surat ini.', 'error');
  }
}

function deleteMail(id) {
  const mail = state.mails.find((item) => item.id === id);
  if (!mail) return;
  const confirmed = confirm(`Hapus surat ${mail.mailNo}?`);
  if (!confirmed) return;
  state.mails = state.mails.filter((item) => item.id !== id);
  persistMails();
  renderMailTables();
  renderDashboards();
  renderCategories();
  if (activeView === 'reports') {
    handleReportSubmit(new Event('submit'));
  }
  displayToast('Data surat berhasil dihapus.', 'success');
}

function populateCategoryOptions() {
  const incomingSelect = document.getElementById('incoming-category');
  const outgoingSelect = document.getElementById('outgoing-category');
  const options = state.categories
    .map((category) => `<option value="${category.id}">${category.name}</option>`)
    .join('');
  const placeholder = '<option value="" disabled selected>Belum ada kategori</option>';

  if (incomingSelect) {
    const previous = incomingSelect.value;
    incomingSelect.innerHTML = options || placeholder;
    if (options && state.categories.some((category) => category.id === previous)) {
      incomingSelect.value = previous;
    } else if (options && state.categories.length) {
      incomingSelect.value = state.categories[0].id;
    }
  }

  if (outgoingSelect) {
    const previous = outgoingSelect.value;
    outgoingSelect.innerHTML = options || placeholder;
    if (options && state.categories.some((category) => category.id === previous)) {
      outgoingSelect.value = previous;
    } else if (options && state.categories.length) {
      outgoingSelect.value = state.categories[0].id;
    }
  }
}

function renderCategories() {
  const tbody = document.getElementById('category-table-body');
  if (!tbody) return;

  tbody.innerHTML = state.categories.length
    ? state.categories
        .map((category) => {
          const total = state.mails.filter((mail) => mail.categoryId === category.id).length;
          return `
            <tr>
              <td>${category.name}</td>
              <td>${category.description}</td>
              <td>${total}</td>
              <td>
                <div class="actions-inline">
                  <button class="btn btn-text" data-action="edit" data-id="${category.id}"><i class="fa-solid fa-pen"></i> Ubah</button>
                  <button class="btn btn-text" data-action="delete" data-id="${category.id}"><i class="fa-solid fa-trash"></i> Hapus</button>
                </div>
              </td>
            </tr>
          `;
        })
        .join('')
    : emptyRowTemplate(4, 'Belum ada kategori surat.');
}

function handleCategorySubmit(event) {
  event.preventDefault();
  const idField = document.getElementById('category-id');
  const nameField = document.getElementById('category-name');
  const descField = document.getElementById('category-description');

  const categoryName = nameField.value.trim();
  const description = descField.value.trim();
  if (!categoryName) {
    displayToast('Nama kategori wajib diisi.', 'error');
    return;
  }

  const existingId = idField.value;
  if (existingId) {
    const category = state.categories.find((item) => item.id === existingId);
    if (!category) return;
    category.name = categoryName;
    category.description = description;
    displayToast('Kategori berhasil diperbarui.', 'success');
  } else {
    const newCategory = {
      id: generateId('cat'),
      name: categoryName,
      description
    };
    state.categories.push(newCategory);
    displayToast('Kategori baru berhasil ditambahkan.', 'success');
  }

  persistCategories();
  renderCategories();
  populateCategoryOptions();
  event.target.reset();
  idField.value = '';
}

function handleCategoryAction(event) {
  const button = event.target.closest('button[data-action]');
  if (!button) return;
  const id = button.dataset.id;
  const action = button.dataset.action;
  const category = state.categories.find((item) => item.id === id);
  if (!category) return;

  if (action === 'edit') {
    document.getElementById('category-id').value = category.id;
    document.getElementById('category-name').value = category.name;
    document.getElementById('category-description').value = category.description;
  } else if (action === 'delete') {
    const related = state.mails.some((mail) => mail.categoryId === id);
    if (related) {
      displayToast('Kategori tidak dapat dihapus karena masih digunakan oleh surat.', 'error');
      return;
    }
    if (confirm(`Hapus kategori ${category.name}?`)) {
      state.categories = state.categories.filter((item) => item.id !== id);
      persistCategories();
      renderCategories();
      populateCategoryOptions();
      displayToast('Kategori berhasil dihapus.', 'success');
    }
  }
}

function renderDashboards() {
  if (currentUser?.role === 'admin') {
    renderAdminDashboard();
  }
  renderStaffDashboard();
}

function renderAdminDashboard() {
  const totalIncoming = state.mails.filter((mail) => mail.type === 'incoming').length;
  const totalOutgoing = state.mails.filter((mail) => mail.type === 'outgoing').length;
  const totalCategories = state.categories.length;
  const totalUsers = state.users.filter((user) => user.status === 'Active').length;

  setElementText('total-incoming', totalIncoming);
  setElementText('total-outgoing', totalOutgoing);
  setElementText('total-categories', totalCategories);
  setElementText('total-users', totalUsers);

  renderMailChart();
  renderLatestMails();
}

function renderStaffDashboard() {
  if (!currentUser) return;
  const myMails = state.mails.filter((mail) => mail.createdBy === currentUser.id);
  const incoming = myMails.filter((mail) => mail.type === 'incoming').length;
  const outgoing = myMails.filter((mail) => mail.type === 'outgoing').length;

  setElementText('staff-total-created', myMails.length);
  setElementText('staff-incoming-count', incoming);
  setElementText('staff-outgoing-count', outgoing);

  const list = document.getElementById('staff-latest-mails');
  if (!list) return;
  list.innerHTML = myMails.length
    ? [...myMails]
        .sort(sortByDateDesc)
        .slice(0, 5)
        .map((mail) => `
          <li>
            <strong>${mail.subject}</strong>
            <span>${formatDate(mail.date)} • ${mail.mailNo}</span>
          </li>
        `)
        .join('')
    : '<li>Tidak ada surat yang Anda kelola.</li>';
}

function renderMailChart() {
  const ctx = document.getElementById('mail-chart');
  if (!ctx) return;

  const incoming = new Array(12).fill(0);
  const outgoing = new Array(12).fill(0);

  state.mails.forEach((mail) => {
    const date = new Date(mail.date);
    if (Number.isNaN(date.getTime())) return;
    const month = date.getMonth();
    if (mail.type === 'incoming') {
      incoming[month] += 1;
    } else {
      outgoing[month] += 1;
    }
  });

  if (mailChartInstance) {
    mailChartInstance.data.datasets[0].data = incoming;
    mailChartInstance.data.datasets[1].data = outgoing;
    mailChartInstance.update();
    return;
  }

  mailChartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: MONTH_LABELS,
      datasets: [
        {
          label: 'Surat Masuk',
          data: incoming,
          backgroundColor: 'rgba(37, 99, 235, 0.8)',
          borderRadius: 8
        },
        {
          label: 'Surat Keluar',
          data: outgoing,
          backgroundColor: 'rgba(16, 185, 129, 0.8)',
          borderRadius: 8
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0
          }
        }
      }
    }
  });
}

function renderLatestMails() {
  const list = document.getElementById('latest-mail-list');
  if (!list) return;

  const latest = [...state.mails]
    .sort(sortByDateDesc)
    .slice(0, 5)
    .map((mail) => {
      const type = mail.type === 'incoming' ? 'Surat Masuk' : 'Surat Keluar';
      return `
        <li>
          <strong>${mail.subject}</strong>
          <span>${formatDate(mail.date)} • ${type} • ${mail.mailNo}</span>
        </li>
      `;
    });

  list.innerHTML = latest.length ? latest.join('') : '<li>Belum ada surat yang tercatat.</li>';
}

function handleReportSubmit(event) {
  if (event) event.preventDefault();
  const shouldNotify = Boolean(event?.isTrusted);

  const type = document.getElementById('report-type').value;
  const period = document.getElementById('report-period').value;
  const periodValue = getReportPeriodValue(period);

  let filtered = [...state.mails];

  if (type !== 'all') {
    filtered = filtered.filter((mail) => mail.type === type);
  }

  if (period === 'daily' && periodValue) {
    filtered = filtered.filter((mail) => mail.date === periodValue);
  } else if (period === 'monthly' && periodValue) {
    filtered = filtered.filter((mail) => mail.date?.startsWith(periodValue));
  } else if (period === 'yearly' && periodValue) {
    filtered = filtered.filter((mail) => mail.date?.startsWith(periodValue));
  }

  reportResults = filtered.map((mail) => ({
    mailNo: mail.mailNo,
    type: mail.type === 'incoming' ? 'Surat Masuk' : 'Surat Keluar',
    party: mail.type === 'incoming' ? mail.sender || '-' : mail.recipient || '-',
    date: formatDate(mail.date),
    subject: mail.subject,
    category: getCategoryName(mail.categoryId)
  }));

  renderReportTable(reportResults);
  if (shouldNotify) {
    displayToast('Laporan diperbarui.', 'success');
  }
}

function getReportPeriodValue(period) {
  if (period === 'daily') {
    return document.getElementById('report-date')?.value || '';
  }
  if (period === 'monthly') {
    return document.getElementById('report-month')?.value || '';
  }
  if (period === 'yearly') {
    return document.getElementById('report-year')?.value || '';
  }
  return '';
}

function renderReportPeriodInput(period) {
  const container = document.getElementById('report-period-fields');
  if (!container) return;

  let template = '';
  if (period === 'daily') {
    template = `
      <label for="report-date">Pilih Tanggal</label>
      <input type="date" id="report-date" required />
    `;
  } else if (period === 'monthly') {
    template = `
      <label for="report-month">Pilih Bulan</label>
      <input type="month" id="report-month" required />
    `;
  } else {
    const currentYear = new Date().getFullYear();
    template = `
      <label for="report-year">Pilih Tahun</label>
      <input type="number" id="report-year" min="2000" max="${currentYear + 5}" value="${currentYear}" required />
    `;
  }

  container.innerHTML = template;
}

function renderReportTable(data) {
  const tbody = document.getElementById('report-table-body');
  if (!tbody) return;

  if (!data.length) {
    tbody.innerHTML = emptyRowTemplate(7, 'Belum ada data untuk periode ini.');
    return;
  }

  tbody.innerHTML = data
    .map((row, index) => `
      <tr>
        <td>${index + 1}</td>
        <td>${row.mailNo}</td>
        <td>${row.type}</td>
        <td>${row.party}</td>
        <td>${row.date}</td>
        <td>${row.subject}</td>
        <td>${row.category}</td>
      </tr>
    `)
    .join('');
}

function exportReportToPdf() {
  if (!reportResults.length) {
    displayToast('Tidak ada data laporan untuk diekspor.', 'error');
    return;
  }
  const doc = new window.jspdf.jsPDF('l', 'pt', 'a4');
  doc.setFontSize(14);
  doc.text('Laporan Surat BPSDM Aceh', 40, 40);
  doc.setFontSize(10);
  doc.text(`Dibuat pada: ${formatDate(new Date().toISOString())}`, 40, 60);
  const body = reportResults.map((row, index) => [
    index + 1,
    row.mailNo,
    row.type,
    row.party,
    row.date,
    row.subject,
    row.category
  ]);
  doc.autoTable({
    head: [['No', 'No. Surat', 'Jenis', 'Pengirim/Penerima', 'Tanggal', 'Perihal', 'Kategori']],
    body,
    startY: 80
  });
  doc.save(`laporan-surat-${Date.now()}.pdf`);
}

function exportReportToExcel() {
  if (!reportResults.length) {
    displayToast('Tidak ada data laporan untuk diekspor.', 'error');
    return;
  }
  const worksheetData = reportResults.map((row, index) => ({
    No: index + 1,
    'No. Surat': row.mailNo,
    Jenis: row.type,
    'Pengirim/Penerima': row.party,
    Tanggal: row.date,
    Perihal: row.subject,
    Kategori: row.category
  }));
  const worksheet = XLSX.utils.json_to_sheet(worksheetData);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Laporan');
  XLSX.writeFile(workbook, `laporan-surat-${Date.now()}.xlsx`);
}

function handleUserSubmit(event) {
  event.preventDefault();
  const name = document.getElementById('user-name').value.trim();
  const email = document.getElementById('user-email').value.trim();
  const username = document.getElementById('user-username').value.trim();
  const password = document.getElementById('user-password').value.trim();
  const role = document.getElementById('user-role').value;

  if (!name || !email || !username || !password) {
    displayToast('Semua kolom wajib diisi.', 'error');
    return;
  }

  const duplicateEmail = state.users.some((user) => user.email.toLowerCase() === email.toLowerCase());
  if (duplicateEmail) {
    displayToast('Email sudah terdaftar.', 'error');
    return;
  }

  const duplicateUsername = state.users.some((user) => user.username.toLowerCase() === username.toLowerCase());
  if (duplicateUsername) {
    displayToast('Username sudah digunakan.', 'error');
    return;
  }

  const newUser = {
    id: generateId('user'),
    name,
    email,
    username,
    password,
    role,
    status: 'Active'
  };

  state.users.push(newUser);
  persistUsers();
  renderUsers();
  renderDashboards();
  event.target.reset();
  displayToast('Pengguna baru berhasil ditambahkan.', 'success');
}

function handleUserAction(event) {
  const button = event.target.closest('button[data-action]');
  if (!button) return;
  const id = button.dataset.id;
  const action = button.dataset.action;
  const user = state.users.find((item) => item.id === id);
  if (!user) return;

  if (action === 'toggle') {
    if (user.id === currentUser.id) {
      displayToast('Anda tidak dapat mengubah status akun sendiri.', 'error');
      return;
    }
    user.status = user.status === 'Active' ? 'Inactive' : 'Active';
    persistUsers();
    renderUsers();
    renderDashboards();
    displayToast('Status pengguna diperbarui.', 'success');
  } else if (action === 'remove') {
    if (user.id === currentUser.id) {
      displayToast('Anda tidak dapat menghapus akun sendiri.', 'error');
      return;
    }
    if (user.role === 'admin') {
      const adminCount = state.users.filter((item) => item.role === 'admin').length;
      if (adminCount <= 1) {
        displayToast('Tidak dapat menghapus admin terakhir.', 'error');
        return;
      }
    }
    if (confirm(`Hapus pengguna ${user.name}?`)) {
      state.users = state.users.filter((item) => item.id !== id);
      persistUsers();
      renderUsers();
      renderDashboards();
      displayToast('Pengguna berhasil dihapus.', 'success');
    }
  }
}

async function handleInstitutionSubmit(event) {
  event.preventDefault();
  const name = document.getElementById('institution-name').value.trim();
  const address = document.getElementById('institution-address-input').value.trim();
  const fileInput = document.getElementById('institution-logo');
  const file = fileInput?.files?.[0];
  let logoData = state.settings.logo || null;

  if (file) {
    logoData = await readFileAsDataUrl(file);
  }

  state.settings.institutionName = name;
  state.settings.address = address;
  state.settings.logo = logoData;
  persistSettings();
  applyInstitutionSettings();
  displayToast('Identitas instansi diperbarui.', 'success');
}

function handleFileSettingsSubmit(event) {
  event.preventDefault();
  const maxSize = Number(document.getElementById('max-file-size').value);
  if (Number.isNaN(maxSize) || maxSize <= 0) {
    displayToast('Masukkan ukuran berkas yang valid.', 'error');
    return;
  }
  state.settings.maxFileSize = maxSize;
  persistSettings();
  displayToast('Pengaturan berkas disimpan.', 'success');
}

function downloadBackup() {
  const backup = {
    timestamp: new Date().toISOString(),
    users: state.users,
    categories: state.categories,
    mails: state.mails,
    settings: state.settings
  };
  const blob = new Blob([JSON.stringify(backup, null, 2)], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `backup-arsip-${Date.now()}.json`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
  displayToast('Backup data berhasil diunduh.', 'success');
}

function handleRestoreUpload(event) {
  const file = event.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = (e) => {
    try {
      const data = JSON.parse(e.target.result);
      if (!data.users || !data.mails || !data.categories || !data.settings) {
        throw new Error('Struktur berkas tidak sesuai.');
      }
      state.users = data.users;
      state.mails = data.mails;
      state.categories = data.categories;
      state.settings = data.settings;
      if (currentUser) {
        const refreshed = state.users.find((item) => item.id === currentUser.id && item.status === 'Active');
        currentUser = refreshed || null;
      }
      if (!currentUser) {
        handleLogout();
        showLoginMessage('Sesi Anda berakhir setelah pemulihan data. Silakan masuk kembali.', 'error');
        return;
      }
      persistUsers();
      persistMails();
      persistCategories();
      persistSettings();
      renderNavigation();
      renderUserInfo();
      applyInstitutionSettings();
      populateCategoryOptions();
      renderMailTables();
      renderCategories();
      renderUsers();
      renderDashboards();
      renderReportTable([]);
      displayToast('Data berhasil dipulihkan.', 'success');
    } catch (error) {
      console.error(error);
      displayToast('Gagal memulihkan data. Pastikan format JSON benar.', 'error');
    }
  };
  reader.readAsText(file);
}

function renderUsers() {
  const tbody = document.getElementById('user-table-body');
  if (!tbody) return;

  tbody.innerHTML = state.users.length
    ? state.users
        .map((user) => `
          <tr>
            <td>${user.name}</td>
            <td>${user.email}</td>
            <td>${user.role === 'admin' ? 'Admin' : 'Staf'}</td>
            <td><span class="status-badge ${user.status === 'Active' ? 'active' : 'inactive'}">${user.status}</span></td>
            <td>
              <div class="actions-inline">
                <button class="btn btn-text" data-action="toggle" data-id="${user.id}"><i class="fa-solid fa-power-off"></i> Ubah Status</button>
                <button class="btn btn-text" data-action="remove" data-id="${user.id}"><i class="fa-solid fa-user-xmark"></i> Hapus</button>
              </div>
            </td>
          </tr>
        `)
        .join('')
    : emptyRowTemplate(5, 'Belum ada pengguna.');
}

function renderSettingsForms() {
  const maxSizeInput = document.getElementById('max-file-size');
  if (maxSizeInput) {
    maxSizeInput.value = state.settings.maxFileSize || 10;
  }
}

function handleLogout() {
  sessionStorage.removeItem('arsip_current_user');
  currentUser = null;
  const loginView = document.getElementById('login-view');
  const appView = document.getElementById('app');
  if (appView) appView.classList.add('hidden');
  if (loginView) loginView.classList.remove('hidden');
  const pageTitle = document.getElementById('page-title');
  if (pageTitle) pageTitle.textContent = 'Dashboard';
  hideLoginMessage();
}

function persistUsers() {
  localStorage.setItem(STORAGE_KEYS.users, JSON.stringify(state.users));
}

function persistCategories() {
  localStorage.setItem(STORAGE_KEYS.categories, JSON.stringify(state.categories));
}

function persistMails() {
  localStorage.setItem(STORAGE_KEYS.mails, JSON.stringify(state.mails));
}

function persistSettings() {
  localStorage.setItem(STORAGE_KEYS.settings, JSON.stringify(state.settings));
}

function readFileAsDataUrl(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = (error) => reject(error);
    reader.readAsDataURL(file);
  });
}

function generateId(prefix) {
  return `${prefix}-${Date.now()}-${Math.random().toString(16).slice(2, 8)}`;
}

function getCategoryName(id) {
  return state.categories.find((category) => category.id === id)?.name || '-';
}

function getUserName(id) {
  return state.users.find((user) => user.id === id)?.name || '-';
}

function formatDate(value) {
  if (!value) return '-';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return value;
  }
  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  }).format(date);
}

function sortByDateDesc(a, b) {
  const aDate = new Date(a.createdAt || a.date);
  const bDate = new Date(b.createdAt || b.date);
  return bDate.getTime() - aDate.getTime();
}

function setElementText(id, value) {
  const element = document.getElementById(id);
  if (element) {
    element.textContent = value;
  }
}

function showLoginMessage(message, type = 'error') {
  const element = document.getElementById('login-message');
  if (!element) return;
  element.textContent = message;
  element.classList.remove('hidden', 'error', 'success');
  element.classList.add(type);
}

function hideLoginMessage() {
  const element = document.getElementById('login-message');
  if (!element) return;
  element.classList.add('hidden');
  element.textContent = '';
}

function displayToast(message, type = 'success') {
  const container = document.getElementById('alert-container');
  if (!container) return;
  const icon = type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation';
  const toast = document.createElement('div');
  toast.className = `alert ${type}`;
  toast.innerHTML = `<i class="fa-solid ${icon}"></i><span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => {
    toast.classList.add('fade-out');
    toast.addEventListener('transitionend', () => toast.remove(), { once: true });
  }, 3500);
}
