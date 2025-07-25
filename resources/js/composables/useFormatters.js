import {isValid, parseISO, subDays} from "date-fns";

export const formatDate = (str) => {
    if (!str) return "";
    return new Date(str).toLocaleDateString("es-ES");
};

export const formatDateTime = (str) => {
    if (!str) return "";
    const date = new Date(str);
    return date.toLocaleString("es-ES", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    });
};

// Devuelve una fecha en formato 'YYYY-MM-DD' para campos tipo input/date
export const formatDateForInput = (date) => {
    if (!date) return "";
    const d = new Date(date);
    return d.toISOString().split("T")[0]; // Devuelve 'YYYY-MM-DD'
};

// useFormatters.js
export const formatDateLocal = (date) => {
    if (!date || isNaN(date.getTime?.())) return "";
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
};

export const formatDateUnix = (unix) => {
    if (!unix) return 'N/A';
    return new Date(unix * 1000).toLocaleString(); // Unix timestamp → JS timestamp
}

export const getModifiedDate = (item) => {
    if (!item?.started_at || !item?.club?.days_min_booking) return null;

    const date = parseISO(item.started_at);
    const days = Number(item.club.days_min_booking);

    if (!isValid(date) || isNaN(days)) return null;

    return subDays(date, days);
};

export const formatDatePhp = (input, format = 'd-m-Y H:i') => {
    const date = new Date(input);
    if (isNaN(date.getTime())) return '';

    const pad = (n) => String(n).padStart(2, '0');

    const map = {
        d: pad(date.getDate()),
        m: pad(date.getMonth() + 1),
        Y: date.getFullYear(),
        y: String(date.getFullYear()).slice(-2),
        H: pad(date.getHours()),
        i: pad(date.getMinutes()),
        s: pad(date.getSeconds()),
        // Extras si lo necesitas
        D: date.toLocaleDateString('es-ES', { weekday: 'short' }),
        l: date.toLocaleDateString('es-ES', { weekday: 'long' }),
        F: date.toLocaleDateString('es-ES', { month: 'long' }),
        M: date.toLocaleDateString('es-ES', { month: 'short' }),
    };

    return format.replace(/d|m|Y|y|H|i|s|D|l|F|M/g, (token) => map[token] || token);
};
