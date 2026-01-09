# Solution for Dragging Ungrouped Parameters

The current issue is that you want to position ungrouped parameters anywhere in the list, including between groups.

## Current Structure:
- Ungrouped section (sortable)
- Group 1 (sortable)
- Group 2 (sortable)
- etc.

## Problem:
You can drag parameters WITHIN each section and BETWEEN sections, but you can't position an ungrouped parameter to appear AFTER Group 1 but BEFORE Group 2.

## Solution Options:

### Option 1: Use sort_order for absolute positioning
Instead of grouping by group_name in the display, sort ALL parameters by `sort_order` and display them in that order, showing group headers when the group changes.

This would allow:
```
Hemoglobin (ungrouped, sort_order=1)
GROUP: Differential Leucocyte Count
  - Neutrophils (grouped, sort_order=2)
  - Lymphocytes (grouped, sort_order=3)
Platelet Count (ungrouped, sort_order=4)
GROUP: RBC Indices  
  - MCV (grouped, sort_order=5)
```

### Option 2: Accept current limitation
The current system works well for most cases. Ungrouped parameters appear at the top, and you can drag them into groups or drag grouped parameters out to make them ungrouped.

## Recommendation:
I recommend **Option 1** - it provides maximum flexibility and matches how the PDF already renders (by sort_order with group headers inline).
