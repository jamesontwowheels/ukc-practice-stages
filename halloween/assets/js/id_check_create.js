document.addEventListener('DOMContentLoaded', async () => {
  const STORAGE_KEY = 'userID';
  let userID = localStorage.getItem(STORAGE_KEY);
  const debugZone = document.getElementById("debug-zone");
  debugZone.innerHTML = "userID = " + userID;

  if (!userID) {
    // No userID in local storage — create one
    try {
      const res = await fetch('assets/php/set_id.php', { method: 'POST' });
      const data = await res.json();
      if (data.status === 'ok' && data.user_ID) {
        userID = data.user_ID;
        debugZone.innerHTML = "new local user " + userID;
        localStorage.setItem(STORAGE_KEY, userID);
      } else {
        console.error('Failed to create user:', data);
        debugZone.innerHTML = "failed to create user";
        return;
      }
    } catch (e) {
      debugZone.innerHTML = "error creating user";
      console.error('Error creating user:', e);
      return;
    }
  } else {
    // ✅ UserID already exists, just sync it to PHP session
    try {
      const syncRes = await fetch('assets/php/save_session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ userID })
      });
      const syncData = await syncRes.json();
      if (syncData.status === 'ok') {
        debugZone.innerHTML = "session synced for user " + userID;
      } else {
        debugZone.innerHTML = "failed to sync session";
        console.error('Failed to sync session:', syncData);
      }
    } catch (err) {
      debugZone.innerHTML = "error syncing session";
      console.error('Error syncing PHP session:', err);
    }
  }

  // Store in session scope for JS use
  window.sessionUserID = userID;
  console.log('Session userID:', window.sessionUserID);
});
