/*!
 * Simple Form Builder for HTMLy - @author danpros
 *
 * const fields = [];
 */
let editingIndex = -1; // Track index of the field being edited (-1 means not editing)

// Elements
const typeEl = document.getElementById('type');
const nameEl = document.getElementById('name');
const labelEl = document.getElementById('label');
const valueEl = document.getElementById('value');
const infoEl = document.getElementById('info'); // New element for 'info'
const optionsContainerEl = document.getElementById('options-container');
const optionListEl = document.getElementById('option-list');
const addOptionBtn = document.getElementById('add-option');
const addFieldBtn = document.getElementById('add-field');
const formPreviewEl = document.getElementById('form-preview');
const jsonOutputEl = document.getElementById('json-output');

// Show or hide the options container and reset fields
typeEl.addEventListener('change', () => {
    nameEl.value = '';
    labelEl.value = '';
    valueEl.value = '';
    infoEl.value = ''; // Reset 'info' field
    optionsContainerEl.style.display = typeEl.value === 'select' ? 'block' : 'none';
    optionListEl.innerHTML = '';
    addFieldBtn.textContent = "Add Field";
});

// Add new option for select field
addOptionBtn.addEventListener('click', () => {
    const optionDiv = document.createElement('div');
    optionDiv.setAttribute('class', 'option-item row mt-1 mb-1');

    const col1Div = document.createElement('div');
    col1Div.classList.add('col');
    
    const col2Div = document.createElement('div');
    col2Div.classList.add('col');
    
    const labelInput = document.createElement('input');
    labelInput.type = 'text';
    labelInput.placeholder = 'Label (required)';
    labelInput.setAttribute('class', 'option-label form-control');

    const valueInput = document.createElement('input');
    valueInput.type = 'text';
    valueInput.placeholder = 'Value (required)';
    valueInput.setAttribute('class', 'option-value form-control');
    valueInput.addEventListener('input', () => {
        valueInput.value = valueInput.value.replace(/\s+/g, ''); // Remove spaces in real-time
    });

    const removeBtn = document.createElement('button');
    removeBtn.textContent = 'Remove';
    removeBtn.addEventListener('click', () => optionDiv.remove());
    removeBtn.setAttribute('class', 'btn btn-danger');

    optionDiv.appendChild(labelInput);
    optionDiv.appendChild(col1Div);
    optionDiv.appendChild(col2Div);    
    col1Div.appendChild(labelInput);
    col2Div.appendChild(valueInput);
    optionDiv.appendChild(removeBtn);
    optionListEl.appendChild(optionDiv);
});

// Add field button logic (works for both adding and updating fields)
addFieldBtn.addEventListener('click', () => {
    let field = {
        type: typeEl.value,
        name: nameEl.value.trim().replace(/\s+/g, ''), // Remove spaces
        label: labelEl.value.trim(),
        value: valueEl.value.trim(),
        info: infoEl.value.trim() // Add the 'info' property
    };

    if (field.type === 'select') {
        const options = Array.from(document.querySelectorAll('.option-item')).map(item => {
            const label = item.querySelector('.option-label').value.trim();
            const value = item.querySelector('.option-value').value.trim().replace(/\s+/g, ''); // Remove spaces
            return { label, value };
        });

        if (options.some(opt => !opt.label || !opt.value)) {
            alert("All options for a select field must have both label and value!");
            return;
        }
        field.options = options;
    }

    if (!field.name || !field.label || !field.type) {
        alert("Please fill in all required fields: Type, Name, and Label.");
        return;
    }

    if (editingIndex === -1) {
        const existingNames = fields.map(f => f.name);
        if (existingNames.includes(field.name)) {
            const timestamp = Date.now();
            field.name = `${field.name}_${timestamp}`;
        }
        fields.push(field);
    } else {
        fields[editingIndex] = field;
        editingIndex = -1;
        addFieldBtn.textContent = "Add Field";
    }

    updatePreviewAndOutput();

    document.getElementById(field.name + '-form-preview').scrollIntoView({
        behavior: 'smooth'
    });        

});

// Delete field logic
function deleteField(index) {
    if (confirm("Are you sure you want to delete this field?")) {
        fields.splice(index, 1);
        updatePreviewAndOutput();
    }
}

// Edit field logic
function editField(index) {
    const field = fields[index];

    typeEl.value = field.type;
    nameEl.value = field.name.replace(/\s+/g, ''); // Remove spaces
    labelEl.value = field.label;
    valueEl.value = field.value || '';
    infoEl.value = field.info || ''; // Populate 'info' input field

    if (field.type === 'select') {
        optionsContainerEl.style.display = 'block';
        optionListEl.innerHTML = '';
        field.options.forEach(opt => {
            const optionDiv = document.createElement('div');
            optionDiv.setAttribute('class', 'option-item row mt-1 mb-1');

            const col1Div = document.createElement('div');
            col1Div.classList.add('col');
            
            const col2Div = document.createElement('div');
            col2Div.classList.add('col');

            const labelInput = document.createElement('input');
            labelInput.type = 'text';
            labelInput.placeholder = 'Label (required)';
            labelInput.value = opt.label;
            labelInput.setAttribute('class', 'option-label form-control');

            const valueInput = document.createElement('input');
            valueInput.type = 'text';
            valueInput.placeholder = 'Value (required)';
            valueInput.value = opt.value.replace(/\s+/g, ''); // Remove spaces
            valueInput.setAttribute('class', 'option-value form-control');
            valueInput.addEventListener('input', () => {
                valueInput.value = valueInput.value.replace(/\s+/g, ''); // Remove spaces in real-time
            });

            const removeBtn = document.createElement('button');
            removeBtn.textContent = 'Remove';
            removeBtn.setAttribute('class', 'btn btn-danger');
            removeBtn.addEventListener('click', () => {
                if (confirm("Are you sure you want to remove this option?")) {
                    optionDiv.remove();
                }
            });

            optionDiv.appendChild(labelInput);
            optionDiv.appendChild(col1Div);
            optionDiv.appendChild(col2Div);    
            col1Div.appendChild(labelInput);
            col2Div.appendChild(valueInput);
            optionDiv.appendChild(removeBtn);
            optionListEl.appendChild(optionDiv);
        });
    } else {
        optionsContainerEl.style.display = 'none';
        optionListEl.innerHTML = '';
    }

    editingIndex = index;
    addFieldBtn.textContent = "Update Field";
    document.getElementById('input-status').innerHTML = `<div class="callout callout-warning">Editing:</small> <code>${field.label}</div>`;
    document.getElementById('form-input').scrollIntoView({
        behavior: 'smooth'
    });
}

// Update preview with Edit and Delete buttons
function updatePreviewAndOutput() {
    formPreviewEl.innerHTML = '';

    fields.forEach((f, index) => {
        const wrapper = document.createElement('div');
        wrapper.setAttribute('class', 'field-preview callout callout-info');
        wrapper.setAttribute('id', f.name + '-form-preview');
        formID = f.name;

        const label = document.createElement('label');
        label.innerHTML = `${f.label} <br><small>Field ID:</small> <code>${f.name}</code>`;
        wrapper.appendChild(label);

        let el;
        if (f.type === 'textarea') {
            el = document.createElement('textarea');
            el.placeholder = f.info;
            el.setAttribute('class', 'form-control');
            el.value = f.value;
        } else if (f.type === 'checkbox') {
            const spacer = document.createElement('br');
            el = document.createElement('input');
            el.type = 'checkbox';
            el.checked = f.value === 'true';
            const checkboxLabel = document.createElement('span');
            checkboxLabel.textContent = ` ${f.label} `;
            wrapper.appendChild(spacer);
            wrapper.appendChild(el);
            wrapper.appendChild(checkboxLabel);
            el = null;
        } else if (f.type === 'select') {
            el = document.createElement('select');
            el.setAttribute('class', 'form-control');
            f.options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.label;
                el.appendChild(option);
            });
        } else {
            el = document.createElement('input');
            el.type = f.type;
            el.value = f.value;
            el.placeholder = f.info;
            el.setAttribute('class', 'form-control');
        }

        if (el) {
            wrapper.appendChild(el);
        }

        if (f.type === 'checkbox' || f.type === 'select') {        
            const tip = document.createElement('span');
            tip.innerHTML = `<small class="d-block mt-1"><em>${f.info}</em></small>`;
            wrapper.appendChild(tip);
        }

        const editBtn = document.createElement('button');
        editBtn.textContent = 'Edit';
        editBtn.addEventListener('click', () => editField(index));
        editBtn.setAttribute('class', 'btn btn-primary btn-xs m-1');
        wrapper.appendChild(editBtn);

        const deleteBtn = document.createElement('button');
        deleteBtn.textContent = 'Delete';
        deleteBtn.addEventListener('click', () => deleteField(index));
        deleteBtn.setAttribute('class', 'btn btn-danger btn-xs m-1');
        wrapper.appendChild(deleteBtn);

        formPreviewEl.appendChild(wrapper);

    });

    jsonOutputEl.value = JSON.stringify(fields, null, 2);
    nameEl.value = '';
    labelEl.value = '';
    valueEl.value = '';
    infoEl.value = ''; // Reset 'info' field
    optionsContainerEl.style.display = 'none';
    optionListEl.innerHTML = '';
    typeEl.value = "text";
    addFieldBtn.textContent = "Add Field";
    document.getElementById('input-status').innerHTML = '';        

}

document.addEventListener('DOMContentLoaded', () => {
    updatePreviewAndOutput();

    // Real-time removal of spaces in the name field
    nameEl.addEventListener('input', () => {
        nameEl.value = nameEl.value.replace(/\s+/g, ''); // Remove spaces in real-time
    });    

    valueEl.addEventListener('input', () => {
        if (typeEl.value === 'select') {
            valueEl.value = valueEl.value.replace(/\s+/g, ''); // Remove spaces
        }
    });    

});
