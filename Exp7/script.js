// Array to store all submitted form entries
const arr = [];

// Clear password error when user re-types
document.getElementById('confirmPassword').addEventListener('input', function () {
    document.getElementById('passwordError').textContent = '';
});
document.getElementById('password').addEventListener('input', function () {
    document.getElementById('passwordError').textContent = '';
});

document.getElementById('submitBtn').addEventListener('click', function () {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const usn = document.getElementById('usn').value.trim();
    const dob = document.getElementById('dob').value;
    const description = document.getElementById('description').value.trim();

    // Gender
    const genderRadios = document.querySelectorAll('input[name="gender"]');
    let gender = 'Not selected';
    genderRadios.forEach(r => { if (r.checked) gender = r.value; });

    // Languages
    const langCheckboxes = document.querySelectorAll('input[name="languages"]:checked');
    const languages = langCheckboxes.length
        ? Array.from(langCheckboxes).map(c => c.value).join(', ')
        : 'None selected';

    // Validation
    if (!name || !email || !usn || !dob) {
        alert('Please fill in all required fields (Name, Email, USN, Date of Birth).');
        return;
    }
    if (password !== confirmPassword) {
        document.getElementById('passwordError').textContent = 'Incorrect password: passwords do not match!';
        document.getElementById('confirmPassword').focus();
        return;
    }

    // Clear any previous error messages
    document.getElementById('passwordError').textContent = '';

    // Push new entry into the array
    arr.push({ name, email, usn, gender, languages, dob, description });

    // Re-render the table
    renderTable();

    // Reset form inputs
    document.getElementById('formContainer').querySelectorAll('input, textarea').forEach(el => {
        if (el.type === 'checkbox' || el.type === 'radio') el.checked = false;
        else el.value = '';
    });
});

function renderTable() {
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';

    if (arr.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="empty-msg">No submissions yet.</td></tr>';
        return;
    }

    arr.forEach((entry, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${entry.name}</td>
            <td>${entry.email}</td>
            <td>${entry.usn}</td>
            <td>${entry.gender}</td>
            <td>${entry.languages}</td>
            <td>${entry.dob}</td>
            <td>${entry.description || '\u2014'}</td>
        `;
        tbody.appendChild(tr);
    });
}

function copyTable() {
    if (arr.length === 0) {
        alert('No data to copy.');
        return;
    }

    const headers = ['#', 'Name', 'Email', 'USN', 'Gender', 'Languages', 'Date of Birth', 'Description'];
    let text = headers.join('\t') + '\n';

    arr.forEach((entry, index) => {
        const row = [index + 1, entry.name, entry.email, entry.usn, entry.gender, entry.languages, entry.dob, entry.description || '\u2014'];
        text += row.join('\t') + '\n';
    });

    navigator.clipboard.writeText(text)
        .then(() => alert('Table copied to clipboard!'))
        .catch(err => console.error('Failed to copy:', err));
}

function exportCSV() {
    if (arr.length === 0) {
        alert('No data to export.');
        return;
    }

    const headers = ['#', 'Name', 'Email', 'USN', 'Gender', 'Languages', 'Date of Birth', 'Description'];
    let csv = headers.join(',') + '\n';

    arr.forEach((entry, index) => {
        const row = [
            index + 1,
            `"${entry.name}"`,
            `"${entry.email}"`,
            `"${entry.usn}"`,
            `"${entry.gender}"`,
            `"${entry.languages}"`,
            `"${entry.dob}"`,
            `"${(entry.description || '').replace(/"/g, '\"\"')}"`
        ];
        csv += row.join(',') + '\n';
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'student_data.csv';
    a.click();
    URL.revokeObjectURL(url);
}