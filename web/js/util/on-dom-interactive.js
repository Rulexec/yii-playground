export function onDomInteractive(fun) {
  if (
    document.readyState === 'interactive' ||
    document.readyState === 'complete'
  ) {
    fun();
    return;
  }

  let called = false;

  document.addEventListener('readystatechange', () => {
    if (called) {
      return;
    }

    called = true;

    if (
      document.readyState == 'interactive' ||
      document.readyState === 'complete'
    ) {
      fun();
    }
  });
}
