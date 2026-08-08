#!/usr/bin/env node

/**
 * HTMLy Model Context Protocol (MCP) Server
 * Enables AI Agents (Hermes-Agent, OpenClaw, Antigravity) to manage HTMLy CMS via Stdio JSON-RPC Tool Calls.
 */

import readline from 'readline';

const HTMLY_SITE_URL = process.env.HTMLY_SITE_URL || 'http://localhost';
const HTMLY_API_KEY = process.env.HTMLY_API_KEY || '';

const TOOLS = [
  {
    name: 'htmly_publish_post',
    description: 'Publish a new blog post or save a draft to HTMLy Flat-File CMS',
    inputSchema: {
      type: 'object',
      properties: {
        title: { type: 'string', description: 'Title of the post' },
        content: { type: 'string', description: 'Markdown content' },
        category: { type: 'string', description: 'Category name' },
        tags: { type: 'string', description: 'Comma separated tags' },
        status: { type: 'string', enum: ['published', 'draft'], default: 'published' }
      },
      required: ['title', 'content']
    }
  },
  {
    name: 'htmly_list_posts',
    description: 'Retrieve published posts or drafts from HTMLy CMS',
    inputSchema: {
      type: 'object',
      properties: {
        status: { type: 'string', enum: ['published', 'draft'], default: 'published' },
        page: { type: 'number', default: 1 },
        limit: { type: 'number', default: 10 }
      }
    }
  },
  {
    name: 'htmly_delete_post',
    description: 'Safely delete a post or draft by slug from HTMLy CMS',
    inputSchema: {
      type: 'object',
      properties: {
        slug: { type: 'string', description: 'Slug of the post to delete' }
      },
      required: ['slug']
    }
  },
  {
    name: 'htmly_get_system_health',
    description: 'Fetch HTMLy CMS server health, disk free space, and telemetry',
    inputSchema: {
      type: 'object',
      properties: {}
    }
  }
];

// Helper for making API requests to HTMLy
async function callHtmlyApi(endpoint, method = 'GET', body = null) {
  const url = `${HTMLY_SITE_URL.replace(/\/$/, '')}/api/v1/${endpoint.replace(/^\//, '')}`;
  const headers = {
    'Authorization': `Bearer ${HTMLY_API_KEY}`,
    'Content-Type': 'application/json'
  };

  const options = { method, headers };
  if (body) {
    options.body = JSON.stringify(body);
  }

  const response = await fetch(url, options);
  return await response.json();
}

// Handle Tool Call Execution
async function handleCallTool(name, args) {
  try {
    if (name === 'htmly_publish_post') {
      const result = await callHtmlyApi('posts', 'POST', args);
      return { content: [{ type: 'text', text: JSON.stringify(result, null, 2) }] };
    }
    
    if (name === 'htmly_list_posts') {
      const query = new URLSearchParams(args).toString();
      const result = await callHtmlyApi(`posts?${query}`, 'GET');
      return { content: [{ type: 'text', text: JSON.stringify(result, null, 2) }] };
    }

    if (name === 'htmly_delete_post') {
      const result = await callHtmlyApi(`posts/${args.slug}`, 'DELETE');
      return { content: [{ type: 'text', text: JSON.stringify(result, null, 2) }] };
    }

    if (name === 'htmly_get_system_health') {
      const result = await callHtmlyApi('system/health', 'GET');
      return { content: [{ type: 'text', text: JSON.stringify(result, null, 2) }] };
    }

    throw new Error(`Unknown tool: ${name}`);
  } catch (err) {
    return {
      isError: true,
      content: [{ type: 'text', text: `MCP Execution Error: ${err.message}` }]
    };
  }
}

// Main MCP JSON-RPC Stdio Loop
const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout,
  terminal: false
});

rl.on('line', async (line) => {
  if (!line.trim()) return;

  try {
    const request = JSON.parse(line);
    const { id, method, params } = request;

    if (method === 'tools/list') {
      const response = {
        jsonrpc: '2.0',
        id,
        result: { tools: TOOLS }
      };
      process.stdout.write(JSON.stringify(response) + '\n');
    } else if (method === 'tools/call') {
      const { name, arguments: toolArgs } = params;
      const result = await handleCallTool(name, toolArgs);
      const response = {
        jsonrpc: '2.0',
        id,
        result
      };
      process.stdout.write(JSON.stringify(response) + '\n');
    }
  } catch (e) {
    // Ignore invalid JSON lines
  }
});
