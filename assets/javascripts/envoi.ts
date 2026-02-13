import '../styles/envoi.scss';

// Create a "close" button and append it to each list item
// const myNodelist = document.getElementsByTagName("LI");

// for (let i = 0; i < myNodelist.length; i++) {
//     const span = document.createElement("SPAN");
//     const txt = document.createTextNode("\u00D7");
//     span.className = "close";
//     span.appendChild(txt);
//     myNodelist[i].appendChild(span);
// }

// Click on a close button to hide the current list item
// const close = document.getElementsByClassName("close");
// for (let i = 0; i < close.length; i++) {
//     close[i].addEventListener('click', (event) => {
//         if (event.target === null)
//             return false;
//         const div = (event.target as HTMLElement).parentElement;
//         if (div !== null)
//             div.style.display = "none";
//     });
// }

// Add a "checked" symbol when clicking on a list item
const list = document.getElementById('task-list');
list?.addEventListener('click', function (event) {
    const target = event.target as HTMLElement;
    console.log(target);
    if (target.tagName === 'LI') {
        target.classList.toggle('checked');
    }
}, false);

// Create a new list item when clicking on the "Add" button
// function newElement() {
//     const li = document.createElement("li");
//     const inputValue = (document.getElementById("myInput") as HTMLInputElement)?.value;
//     const t = document.createTextNode(inputValue);
//     li.appendChild(t);
//     if (inputValue === '') {
//         alert("You must write something!");
//     } else {
//         document.getElementById("task-list")?.appendChild(li);
//     }
//     (document.getElementById("myInput") as HTMLInputElement).value = "";

//     const span = document.createElement("SPAN");
//     const txt = document.createTextNode("\u00D7");
//     span.className = "close";
//     span.appendChild(txt);
//     li.appendChild(span);

//     for (let i = 0; i < close.length; i++) {
//         close[i].addEventListener('click', (event) => {
//             const target = event.target as HTMLElement;
//             const div = target.parentElement;
//             if (div !== null)
//                 div.style.display = "none";
//         });
//     }
// } 