export const formatDate = (str) => {
    if (!str) return "";
    return new Date(str).toLocaleDateString("es-ES");
};

// Devuelve una fecha en formato 'YYYY-MM-DD' para campos tipo input/date
export const formatDateForInput = (date) => {
    if (!date) return "";
    const d = new Date(date);
    return d.toISOString().split("T")[0]; // Devuelve 'YYYY-MM-DD'
};

// useFormatters.js
export const formatDateLocal = (date) => {
    if (!date) return "";

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");

    return `${year}-${month}-${day}`; // Ej: "2025-06-28"
};
