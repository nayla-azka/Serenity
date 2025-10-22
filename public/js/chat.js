//chat.js
function escapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function formatDateLabel(dateStr, userLocale) {
    const today = new Date();
    const yesterday = new Date();
    yesterday.setDate(today.getDate() - 1);

    const todayStr = today.toISOString().slice(0, 10);
    const yesterdayStr = yesterday.toISOString().slice(0, 10);

    if (dateStr === todayStr) {
        return new Intl.RelativeTimeFormat(userLocale, { numeric: "auto" }).format(0, "day");
    } else if (dateStr === yesterdayStr) {
        return new Intl.RelativeTimeFormat(userLocale, { numeric: "auto" }).format(-1, "day");
    } else {
        const [y, m, d] = dateStr.split("-");
        const msgDate = new Date(y, m - 1, d);
        return msgDate.toLocaleDateString(userLocale, {
            weekday: "long",
            year: "numeric",
            month: "short",
            day: "numeric"
        });
    }
}

function appendDateSeparator(container, dateText) {
    const separatorHtml = `<div class="chat-date-separator">${dateText}</div>`;
    container.insertAdjacentHTML("beforeend", separatorHtml);
}

function appendMessage(container, msg, lastDateRef, currentUserRole, userLocale) {
    if (lastDateRef.value !== msg.date) {
        appendDateSeparator(container, formatDateLabel(msg.date, userLocale));
        lastDateRef.value = msg.date;
    }

    const isOwn = (msg.sender_type === "student" && currentUserRole === "siswa")
               || (msg.sender_type === "counselor" && currentUserRole === "counselor");

    const bubbleHtml = `
        <div class="mb-3 ${isOwn ? "text-end" : "text-start"}">
            <div class="chat-bubble ${isOwn ? "own" : "other"}">
                <div class="sender">${msg.sender_name}</div>
                <div>${escapeHtml(msg.message)}</div>
                <span class="time">${msg.sent_at}</span>
            </div>
        </div>
    `;

    container.insertAdjacentHTML("beforeend", bubbleHtml);
    container.scrollTop = container.scrollHeight;
}
