/**
 * Ensures a FastAPI JWT exists in localStorage for the logged-in PHP user.
 * Auto-provisions the user in the Python DB if missing.
 */
async function ensureAccessToken() {
    let token = localStorage.getItem('access_token');
    if (token) return token;

    const email = window.ACADEMY_USER_EMAIL || '';
    const name = window.ACADEMY_USER_NAME || '';
    if (!email) return null;

    try {
        const res = await fetch('http://127.0.0.1:8001/auth/login-via-session', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, name })
        });
        if (res.ok) {
            const data = await res.json();
            if (data.access_token) {
                localStorage.setItem('access_token', data.access_token);
                return data.access_token;
            }
        }
    } catch (e) {
        console.warn('FastAPI auth unavailable:', e);
    }
    return null;
}

function authHeaders() {
    const token = localStorage.getItem('access_token');
    const headers = { 'Content-Type': 'application/json' };
    if (token) headers['Authorization'] = `Bearer ${token}`;
    return headers;
}
