// Carga todas las imágenes de la carpeta y subcarpetas
const imageModules = import.meta.glob('@/assets/img/**/*.{png,jpg,jpeg,svg,gif}', {
    eager: true
});

const images = {};

for (const path in imageModules) {
    // Extraer el nombre base del archivo (sin ruta ni extensión)
    const fileName = path.split('/').pop(); // ej. logo.png
    const name = fileName.split('.')[0];    // ej. logo
    images[name] = imageModules[path].default;
}

export default images;
