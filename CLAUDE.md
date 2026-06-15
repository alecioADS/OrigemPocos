# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**ORIGEM** — Design System V2.0 for a Poços Artesianos (Artesian Wells) application.
Academic project (Projeto Integrador — ADS course).

This repository currently holds design assets only:
- `Pi - Origem.fig` — Figma source file with all screens and components
- `paleta-cores-origem.pdf` — Design System V2.0 color and typography reference

No code exists yet. When implementation begins, this file should be updated with build/run commands and architecture notes.

---

## Design System — ORIGEM V2.0

### Color Palette

**Primary**
| Token | Hex | Usage |
|---|---|---|
| Verde Escuro | `#2F3E2F` | Brand primary — headers, titles, institutional icons |
| Azul Água *(new)* | `#2A7FAF` | Support primary — highlights and drilling-service icons |

**Secondary**
| Token | Hex | Usage |
|---|---|---|
| Verde Médio | `#6E8B6E` | Secondary elements, hover states |
| Verde Vibrante | `#12BA52` | Success, confirmations, detail icons |
| Verde Claro | `#B6FDC1` | Light backgrounds, badges, soft highlights |
| Azul Claro *(new)* | `#A8D8F0` | Water-related section backgrounds, info badges |

**CTA / Highlight**
| Token | Hex | Usage |
|---|---|---|
| Laranja | `#F58C21` | Primary CTA buttons, attention elements |

**Neutral**
| Token | Hex | Usage |
|---|---|---|
| Cinza Escuro *(updated from #333840)* | `#3D4550` | Main body text, paragraphs |
| Cinza Médio *(updated from #99A1AB)* | `#7A8694` | Secondary text, legends, auxiliary info |
| Cinza Claro *(new)* | `#D1D5DB` | Borders, dividers, structural elements |

**Backgrounds**
| Token | Hex | Usage |
|---|---|---|
| Bege Claro | `#FFF2DA` | Section backgrounds, soft highlights |
| Branco | `#FFFFFF` | Cards, containers, main sections |

### Typography

**Typeface:** Inter (Light · Regular · Medium · SemiBold · Bold · ExtraBold)

| Element | Weight | Color |
|---|---|---|
| H1 | Bold | `#2F3E2F` |
| H2 | SemiBold | `#2F3E2F` |
| Body paragraph | Regular | `#3D4550` |
| Secondary / captions | Regular | `#7A8694` |

### Button Styles

| Variant | Background | Text / Border |
|---|---|---|
| Primary | `#F58C21` | `#FFFFFF` |
| Secondary | `#12BA52` | `#FFFFFF` |
| Blue (outline) | transparent | `#2A7FAF` border + text |
| Success | `#B6FDC1` | `#2F3E2F` |
| Neutral | `#3D4550` | `#FFFFFF` |
