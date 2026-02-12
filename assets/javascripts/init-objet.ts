import '../styles/init-objet.scss';

import axios from 'axios';

interface Task {
    id: string;
    text: string;
    status: string;
}
const // 
    prefix = 'transit',
    addTaskButton = document.getElementById('add-task-button') as HTMLButtonElement,
    sauverObjetButton = document.getElementById('sauver-objet-button') as HTMLButtonElement,
    sauverObjetButton_loader = document.getElementById('sauver-objet-button--loader') as HTMLDivElement;

addTaskButton.addEventListener('click', addTask);

document.addEventListener('click', (event) => {
    if ((event.target as HTMLElement).classList.contains('delete-btn')) {
        deleteTask(event.target as HTMLButtonElement);
    }
});

sauverObjetButton.addEventListener('click', () => {
    sauverObjetButton_loader.classList.remove('fr-hidden');
    const // 
        objet_id = sauverObjetButton.dataset.id,
        items = saveOrder(),
        etapes = items.map((item, i) => {
            return {
                rang: i,
                libelle: item.text,
                statut: item.status
            }
        });

    axios.post(`/${prefix}/sauver-actions`, { objet_id, etapes })

    // @TODO: finir envoi AJAX

});

const todoList = document.getElementById('todoList') as HTMLUListElement;
let draggedItem: HTMLElement | null = null;

function addTask(): void {
    const taskInput = document.getElementById('taskInput') as HTMLInputElement;
    const statusInput = document.getElementById('statusInput') as HTMLInputElement;

    if (taskInput.value.trim() === "") return;

    const li = document.createElement('li');
    li.classList.add('todo-item');
    li.classList.add('fr-grid-row');
    li.classList.add('fr-grid-row--gutters');

    li.draggable = true;
    li.id = `task-${Date.now()}`;

    li.innerHTML = /*HTML*/`
        <span class="drag-handle fr-col-1">☰</span>
        <input type="text" value="${taskInput.value}" class="edit-text fr-col-5">
        <input type="text" value="${statusInput.value}" list="statusSuggestions" class="edit-status fr-col-4">
        <button class="delete-btn fr-btn fr-icon-close-line fr-btn--icon-left fr-btn--tertiary-no-outline fr-col-1">Suppr.</button>
    `;

    addDragEvents(li);
    todoList.appendChild(li);

    // Reset des champs
    taskInput.value = "";
    statusInput.value = "";

    sauverObjetButton.removeAttribute('disabled');
}

function deleteTask(btn: HTMLButtonElement) {
    btn?.parentElement?.remove();
    saveOrder();
}

function addDragEvents(item: HTMLElement): void {
    item.addEventListener('dragstart', () => {
        draggedItem = item;
        setTimeout(() => item.classList.add('dragging'), 0);
    });

    item.addEventListener('dragend', () => {
        item.classList.remove('dragging');
        draggedItem = null;
        saveOrder(); // Optionnel : pour sauvegarder l'ordre en BDD
    });

    item.addEventListener('dragover', (e: DragEvent) => {
        e.preventDefault();
        const afterElement = getDragAfterElement(todoList, e.clientY);
        if (afterElement == null) {
            todoList.appendChild(draggedItem!);
        } else {
            todoList.insertBefore(draggedItem!, afterElement);
        }
    });
}

function getDragAfterElement(container: HTMLElement, y: number): HTMLElement | null {
    const draggableElements = [...container.querySelectorAll('.todo-item:not(.dragging)')] as HTMLElement[];

    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY } as { offset: number; element: HTMLElement | null }).element;
}

function saveOrder(): Task[] {
    const items = [...todoList.querySelectorAll('.todo-item')].map(item => ({
        id: item.id,
        text: (item.querySelector('.edit-text') as HTMLInputElement).value,
        status: (item.querySelector('.edit-status') as HTMLInputElement).value
    }));

    if (!items.length) {
        sauverObjetButton.disabled = true;
    }
    console.log("Nouvel ordre :", items);
    return items;
}