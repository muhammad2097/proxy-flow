// src/utils/flags.ts
export const getFlagSvgUrl = (code: string): string => {
  return `https://flagcdn.com/24x18/${code.toLowerCase()}.png`;
};