
document.addEventListener('DOMContentLoaded', async () => {
  const STORAGE_KEY = 'userID';
  let userID = localStorage.getItem(STORAGE_KEY);

  if (!userID) {
    try {
      const res = await fetch('assets/php/set_id.php', { method: 'POST' });
      const data = await res.json();
      if (data.status === 'ok' && data.user_ID) {
        userID = data.user_ID;
        localStorage.setItem(STORAGE_KEY, userID);
      } else {
        console.error('Failed to create user:', data);
        return;
      }
    } catch (e) {
      console.error('Error creating user:', e);
      return;
    }
  }

  // Store in session scope for JS use
  window.sessionUserID = userID;
  console.log('Session userID:', window.sessionUserID);
});
