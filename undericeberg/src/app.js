document.addEventListener('DOMContentLoaded', () => {
  // 1. Upvote handling per post
  document.querySelectorAll('.btn-upvote[data-post-id]').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const postId = btn.getAttribute('data-post-id');
      const numSpan = btn.closest('.upvote-col')?.querySelector('.upvote-num') || btn.querySelector('.upvote-num');

      try {
        const formData = new FormData();
        formData.append('action', 'upvote_post');
        formData.append('post_id', postId);

        const res = await fetch('api.php?json=1', {
          method: 'POST',
          body: formData,
          headers: { 'Accept': 'application/json' }
        });

        if (res.status === 401) {
          window.location.href = 'login.php';
          return;
        }

        const data = await res.json();
        if (data.success) {
          if (data.upvoted) {
            btn.classList.add('voted');
            if (numSpan) {
              numSpan.classList.add('voted');
              numSpan.textContent = data.count;
            }
          } else {
            btn.classList.remove('voted');
            if (numSpan) {
              numSpan.classList.remove('voted');
              numSpan.textContent = data.count;
            }
          }
        }
      } catch (err) {
        console.error('Upvote error:', err);
      }
    });
  });

  // 2. Upvote handling per commenti
  document.querySelectorAll('.btn-upvote[data-comment-id]').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const commId = btn.getAttribute('data-comment-id');
      const countEl = btn.querySelector('.comm-upvote-count') || btn;

      try {
        const formData = new FormData();
        formData.append('action', 'upvote_comment');
        formData.append('comment_id', commId);

        const res = await fetch('api.php?json=1', {
          method: 'POST',
          body: formData,
          headers: { 'Accept': 'application/json' }
        });

        if (res.status === 401) {
          window.location.href = 'login.php';
          return;
        }

        const data = await res.json();
        if (data.success) {
          if (data.upvoted) {
            btn.classList.add('voted');
          } else {
            btn.classList.remove('voted');
          }
          if (countEl) {
            countEl.textContent = `▲ ${data.count}`;
          }
        }
      } catch (err) {
        console.error('Comment upvote error:', err);
      }
    });
  });

  // 3. Layyd stream button (+1 Play counter)
  const layydBtn = document.getElementById('btn-play-layyd');
  if (layydBtn) {
    layydBtn.addEventListener('click', async (e) => {
      const artId = layydBtn.getAttribute('data-artista-id') || 0;
      const countDisplay = document.getElementById('layyd-play-count');

      try {
        const formData = new FormData();
        formData.append('action', 'play_layyd');
        formData.append('artista_id', artId);

        const res = await fetch('api.php?json=1', {
          method: 'POST',
          body: formData,
          headers: { 'Accept': 'application/json' }
        });

        const data = await res.json();
        if (data.success) {
          if (countDisplay) {
            countDisplay.textContent = data.plays;
            countDisplay.classList.add('pulse');
            setTimeout(() => countDisplay.classList.remove('pulse'), 500);
          }
          layydBtn.innerHTML = `<span>▶</span> Ascolto registrato! (${data.plays})`;
          setTimeout(() => {
            layydBtn.innerHTML = `<span>▶</span> Ascolta nonSoCosaSia! (+1 Play)`;
          }, 2000);
        }
      } catch (err) {
        console.error('Play increment error:', err);
      }
    });
  }
});
