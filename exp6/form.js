const form = document.getElementById("eventForm");
const output = document.getElementById("output");

form.addEventListener("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(form);

    let html = "<h3>Registration Output</h3><br>";

    const dataObj = {};
    for (let [key, value] of formData.entries()) {
        if (!dataObj[key]) {
            dataObj[key] = [];
        }
        dataObj[key].push(value);
    }

    // Print values in the format "Key -> Value"
    for (const key in dataObj) {
        let values = dataObj[key];

        let displayValue = values.map(v => {
            if (v instanceof File) return v.name;
            return v;
        }).join(", ");

        if (displayValue && displayValue !== "") {
            html += `${key} -> ${displayValue}<br>`;
        }
    }

    output.innerHTML = html;
    output.style.display = "block"; // Reveal the previously hidden div
    output.scrollIntoView({ behavior: 'smooth' });
});