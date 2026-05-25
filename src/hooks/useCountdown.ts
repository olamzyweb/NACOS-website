import { useState, useEffect } from "react";

interface CountdownResult {
  days: number;
  hours: number;
  minutes: number;
  seconds: number;
  expired: boolean;
}

export function useCountdown(targetDate: Date): CountdownResult {
  const calc = (): CountdownResult => {
    const diff = targetDate.getTime() - Date.now();
    if (diff <= 0) return { days: 0, hours: 0, minutes: 0, seconds: 0, expired: true };
    const days = Math.floor(diff / 86_400_000);
    const hours = Math.floor((diff % 86_400_000) / 3_600_000);
    const minutes = Math.floor((diff % 3_600_000) / 60_000);
    const seconds = Math.floor((diff % 60_000) / 1_000);
    return { days, hours, minutes, seconds, expired: false };
  };

  const [state, setState] = useState<CountdownResult>(calc);

  useEffect(() => {
    const id = setInterval(() => setState(calc()), 1000);
    return () => clearInterval(id);
  }, [targetDate]);

  return state;
}
