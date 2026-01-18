let currentCity = '';

function loadXMLDoc(filename) {
    return new Promise((resolve, reject) => {
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
            if (this.status === 200) {
                resolve(this.responseXML);
            } else {
                reject("Erreur lors du chargement du fichier " + filename);
            }
        };
        xhttp.onerror = () => reject("Erreur réseau");
        xhttp.open("GET", filename, true);
        xhttp.send();
    });
}

async function loadCityData(city) {
    try {
        const xml = await loadXMLDoc('xml/' + city + '.xml');
        const xsl = await loadXMLDoc('xsl/Ville.xsl');
        currentCity = city;  // Stocke le nom de la ville
        
        if (window.ActiveXObject || "ActiveXObject" in window) { // Pour IE
            const ex = xml.transformNode(xsl);
            document.getElementById("content").innerHTML = ex;
        } else if (document.implementation && document.implementation.createDocument) { // Pour Chrome, Firefox, Opera, etc.
            const xsltProcessor = new XSLTProcessor();
            xsltProcessor.importStylesheet(xsl);
            const resultDocument = xsltProcessor.transformToFragment(xml, document);
            document.getElementById("content").appendChild(resultDocument);
        }
    } catch (error) {
        document.getElementById("content").innerHTML = error;
    }
}

function initialize() {
    const urlParams = new URLSearchParams(window.location.search);
    const city = urlParams.get('ville');
    if (city) {
        loadCityData(city);
    } else {
        document.getElementById("content").innerHTML = "Ville non spécifiée.";
    }
}

function generatePDF(event) {
    event.preventDefault(); // Empêche le rechargement de la page
    const { jsPDF } = window.jspdf;
    const contentElement = document.getElementById('content');
    const doc = new jsPDF();

    const title = contentElement.querySelector('h1').textContent;
    const description = contentElement.querySelector('p').textContent;

    doc.setFontSize(24);
    doc.text(title, 10, 20);

    doc.setFontSize(18);
    doc.text("Description", 10, 40);

    doc.setFontSize(12);
    let splitDescription = doc.splitTextToSize(description, 180);
    doc.text(splitDescription, 10, 50);

    let currentY = 70 + splitDescription.length * 10; 

    const sections = contentElement.querySelectorAll('h2');
    sections.forEach(section => {
        if (currentY > 270) {
            doc.addPage();
            currentY = 20;
        }
        doc.setFontSize(18);
        if (section.textContent !== "Description") { 
            doc.text(section.textContent, 10, currentY);
            currentY += 10;
        }

        const items = section.nextElementSibling.querySelectorAll('li');
        doc.setFontSize(12);
        items.forEach(item => {
            if (currentY > 270) {
                doc.addPage();
                currentY = 20;
            }
            const imgElement = item.querySelector('img');
            if (imgElement) {
                const imgData = imgElement.src;
                doc.addImage(imgData, 'JPEG', 10, currentY, 50, 50);
                currentY += 60;
            }
            doc.text("- " + item.textContent, 10, currentY);
            currentY += 10;
        });

        currentY += 10;
    });

    // Ajouter les informations de transport (gares et aéroports)
    const garesSection = contentElement.querySelector('ul');
    const aéroportsSection = contentElement.querySelector('ul');

    if (currentY > 270) {
        doc.addPage();
        currentY = 20;
    }

   
    

    

    doc.save(`${currentCity}.pdf`);
}

document.addEventListener('DOMContentLoaded', initialize);
